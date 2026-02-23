<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\SystemSetting;
use App\Services\ArabicShaper;
use App\Services\WhatsAppService;
use App\Services\StockManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockTransferController extends Controller
{
    protected $stockService;
    protected $whatsappService;
    protected $arabicShaper;

    public function __construct(StockManagementService $stockService, WhatsAppService $whatsappService, ArabicShaper $arabicShaper)
    {
        $this->stockService = $stockService;
        $this->whatsappService = $whatsappService;
        $this->arabicShaper = $arabicShaper;
    }

    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'requestedBy'])
            ->latest()
            ->paginate(10);
        return view('inventory.stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('inventory.stock-transfers.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transfer_date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::beginTransaction();

            $documentNumber = 'TR-' . date('Y') . '-' . str_pad(StockTransfer::count() + 1, 5, '0', STR_PAD_LEFT);

            $transfer = StockTransfer::create([
                'company_id' => session('active_company_id'),
                'document_number' => $documentNumber,
                'transfer_date' => $request->transfer_date,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.stock-transfers.show', $transfer)
                ->with('success', __('messages.stock_transfer_initiated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transfer = StockTransfer::with(['items.product', 'fromWarehouse', 'toWarehouse', 'requestedBy', 'approvedBy', 'receivedBy'])->findOrFail($id);
        return view('inventory.stock-transfers.show', compact('transfer'));
    }

    public function approve($id)
    {
        $transfer = StockTransfer::findOrFail($id);
        if ($transfer->status !== 'pending')
            return back()->with('error', 'Cannot approve.');

        $transfer->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', __('messages.record_updated'));
    }

    public function receive($id)
    {
        $transfer = StockTransfer::with('items')->findOrFail($id);
        if ($transfer->status !== 'approved')
            return back()->with('error', 'Must be approved first.');

        try {
            DB::beginTransaction();

            // Record movements
            foreach ($transfer->items as $item) {
                // Out from source
                $this->stockService->recordMovement([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $transfer->from_warehouse_id,
                    'movement_type' => 'out',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->product->average_cost ?? 0,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $transfer->id,
                    'reference_number' => $transfer->document_number,
                    'notes' => 'Transfer to ' . $transfer->toWarehouse->name
                ]);

                // In to destination
                $this->stockService->recordMovement([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $transfer->to_warehouse_id,
                    'movement_type' => 'in',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->product->average_cost ?? 0,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $transfer->id,
                    'reference_number' => $transfer->document_number,
                    'notes' => 'Transfer from ' . $transfer->fromWarehouse->name
                ]);
            }

            $transfer->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', __('messages.stock_transfer_received'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        try {
            $transfer = StockTransfer::with(['items.product', 'fromWarehouse', 'toWarehouse', 'requestedBy', 'company'])->findOrFail($id);

            // Base64 logo for PDF
            $logoBase64 = null;
            if ($transfer->company?->logo) {
                $path = public_path('storage/' . $transfer->company->logo);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            // Reshape Arabic text for PDF
            if ($transfer->company) {
                $transfer->company_name_ar = $this->arabicShaper->shape($transfer->company->name_ar ?? $transfer->company->name_en);
            }

            foreach ($transfer->items as $item) {
                $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
            }

            $pdf = Pdf::loadView('inventory.stock-transfers.pdf', compact('transfer', 'logoBase64'));
            return $pdf->stream("transfer_{$transfer->document_number}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'PDF Error: ' . $e->getMessage());
        }
    }

    public function sendWhatsApp($id)
    {
        // For transfers, we might notify the recipient warehouse manager or requester
        // Let's implement basic WhatsApp link for now as specific recipient choice logic is TBD
        $transfer = StockTransfer::findOrFail($id);
        $message = "Stock Transfer #{$transfer->document_number} from {$transfer->fromWarehouse->name} to {$transfer->toWarehouse->name}";
        $url = "https://wa.me/?text=" . urlencode($message);
        return redirect()->away($url);
    }
}
