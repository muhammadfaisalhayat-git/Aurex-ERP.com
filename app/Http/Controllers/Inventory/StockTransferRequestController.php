<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockTransferRequest;
use App\Models\StockTransferRequestItem;
use App\Models\Warehouse;
use App\Models\SystemSetting;
use App\Services\WhatsAppService;
use App\Services\ArabicShaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockTransferRequestController extends Controller
{
    protected $whatsappService;
    protected $arabicShaper;

    public function __construct(WhatsAppService $whatsappService, ArabicShaper $arabicShaper)
    {
        $this->whatsappService = $whatsappService;
        $this->arabicShaper = $arabicShaper;
    }

    public function index()
    {
        $requests = StockTransferRequest::with(['fromWarehouse', 'toWarehouse', 'requestedBy'])
            ->latest()
            ->paginate(10);
        return view('inventory.transfer-requests.index', compact('requests'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('inventory.transfer-requests.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.measurement_unit_id' => 'required|exists:measurement_units,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::beginTransaction();
            $documentNumber = 'STR-' . date('Y') . '-' . str_pad(StockTransferRequest::count() + 1, 5, '0', STR_PAD_LEFT);

            $transferRequest = StockTransferRequest::create([
                'company_id' => session('active_company_id'),
                'document_number' => $documentNumber,
                'request_date' => $request->request_date,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                StockTransferRequestItem::create([
                    'stock_transfer_request_id' => $transferRequest->id,
                    'product_id' => $item['product_id'],
                    'measurement_unit_id' => $item['measurement_unit_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.transfer-requests.show', $transferRequest)
                ->with('success', __('messages.record_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $request = StockTransferRequest::with(['items.product', 'items.measurementUnit', 'fromWarehouse', 'toWarehouse', 'requestedBy', 'approvedBy'])->findOrFail($id);
        return view('inventory.transfer-requests.show', compact('request'));
    }

    public function approve($id)
    {
        $request = StockTransferRequest::findOrFail($id);
        if ($request->status !== 'pending')
            return back()->with('error', 'Cannot approve.');

        $request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', __('messages.transfer_request_approved'));
    }

    public function reject($id)
    {
        $request = StockTransferRequest::findOrFail($id);
        if ($request->status !== 'pending')
            return back()->with('error', 'Cannot reject.');

        $request->update(['status' => 'rejected']);
        return back()->with('success', __('messages.transfer_request_rejected'));
    }

    public function execute($id)
    {
        $transferRequest = StockTransferRequest::with('items')->findOrFail($id);
        if ($transferRequest->status !== 'approved')
            return back()->with('error', 'Must be approved first.');

        try {
            DB::beginTransaction();

            // Create a real StockTransfer from this request
            $documentNumber = 'TR-' . date('Y') . '-' . str_pad(StockTransfer::count() + 1, 5, '0', STR_PAD_LEFT);
            $transfer = StockTransfer::create([
                'company_id' => $transferRequest->company_id,
                'document_number' => $documentNumber,
                'transfer_date' => now(),
                'from_warehouse_id' => $transferRequest->from_warehouse_id,
                'to_warehouse_id' => $transferRequest->to_warehouse_id,
                'status' => 'pending',
                'requested_by' => $transferRequest->requested_by,
                'notes' => 'Generated from Request: ' . $transferRequest->document_number,
            ]);

            foreach ($transferRequest->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'quantity' => $item->quantity,
                    'notes' => $item->notes,
                ]);
            }

            $transferRequest->update(['status' => 'executed']);

            DB::commit();
            return redirect()->route('inventory.stock-transfers.show', $transfer)
                ->with('success', __('messages.transfer_initiated_from_request'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        try {
            $request = StockTransferRequest::with(['items.product', 'items.measurementUnit', 'fromWarehouse', 'toWarehouse', 'requestedBy', 'company'])->findOrFail($id);

            // Base64 logo for PDF
            $logoBase64 = null;
            if ($request->company?->logo) {
                $path = public_path('storage/' . $request->company->logo);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            // Reshape Arabic text for PDF
            if ($request->company) {
                $request->company_name_ar = $this->arabicShaper->shape($request->company->name_ar ?? $request->company->name_en);
            }

            foreach ($request->items as $item) {
                $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
            }

            $pdf = Pdf::loadView('inventory.transfer-requests.pdf', compact('request', 'logoBase64'));
            return $pdf->stream("request_{$request->document_number}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sendWhatsApp($id)
    {
        $request = StockTransferRequest::findOrFail($id);
        $message = "Transfer Request #{$request->document_number} from {$request->fromWarehouse->name} to {$request->toWarehouse->name}";
        $url = "https://wa.me/?text=" . urlencode($message);
        return redirect()->away($url);
    }
}
