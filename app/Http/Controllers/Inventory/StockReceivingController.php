<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockReceiving;
use App\Models\StockReceivingItem;
use App\Models\SystemSetting;
use App\Models\Warehouse;
use App\Models\Vendor;
use App\Services\AccountingService;
use App\Services\ArabicShaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppService;
use App\Services\StockManagementService;

class StockReceivingController extends Controller
{
    protected $accountingService;
    protected $whatsappService;
    protected $stockService;
    protected $arabicShaper;

    public function __construct(AccountingService $accountingService, WhatsAppService $whatsappService, StockManagementService $stockService, ArabicShaper $arabicShaper)
    {
        $this->accountingService = $accountingService;
        $this->whatsappService = $whatsappService;
        $this->stockService = $stockService;
        $this->arabicShaper = $arabicShaper;
    }

    public function index()
    {
        $receivings = StockReceiving::with(['warehouse', 'vendor', 'creator'])
            ->latest()
            ->paginate(10);
        return view('inventory.stock-receiving.index', compact('receivings'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $vendors = Vendor::all();
        return view('inventory.stock-receiving.create', compact('warehouses', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiving_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::beginTransaction();

            // Generate document number
            $documentNumber = 'SR-' . date('Y') . '-' . str_pad(StockReceiving::count() + 1, 5, '0', STR_PAD_LEFT);

            $receiving = StockReceiving::create([
                'company_id' => session('active_company_id'),
                'document_number' => $documentNumber,
                'receiving_date' => $request->receiving_date,
                'warehouse_id' => $request->warehouse_id,
                'vendor_id' => $request->vendor_id,
                'purchase_order_number' => $request->purchase_order_number,
                'delivery_note_number' => $request->delivery_note_number,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                StockReceivingItem::create([
                    'stock_receiving_id' => $receiving->id,
                    'product_id' => $item['product_id'],
                    'ordered_quantity' => $item['quantity'],
                    'received_quantity' => 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.stock-receiving.show', $receiving)
                ->with('success', __('messages.stock_transfer_initiated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating stock receiving: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $receiving = StockReceiving::with(['items.product', 'warehouse', 'vendor', 'creator', 'receiver'])->findOrFail($id);
        return view('inventory.stock-receiving.show', compact('receiving'));
    }

    public function receive($id)
    {
        try {
            $receiving = StockReceiving::findOrFail($id);

            if ($receiving->post()) {
                return back()->with('success', __('messages.stock_received_successfully'));
            }

            return back()->with('error', 'Only pending receipts can be received.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error during receiving: ' . $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        try {
            $receiving = StockReceiving::with(['items.product', 'warehouse', 'vendor', 'creator', 'company'])->findOrFail($id);

            // Base64 logo for PDF
            $logoBase64 = null;
            if ($receiving->company?->logo) {
                $path = public_path('storage/' . $receiving->company->logo);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            // Reshape Arabic text for PDF
            if ($receiving->company) {
                $receiving->company_name_ar = $this->arabicShaper->shape($receiving->company->name_ar ?? $receiving->company->name_en);
            }

            if ($receiving->vendor) {
                $receiving->vendor_name_ar = $this->arabicShaper->shape($receiving->vendor->name_ar ?? $receiving->vendor->name);
            }

            foreach ($receiving->items as $item) {
                $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
            }

            $pdf = Pdf::loadView('inventory.stock-receiving.pdf', compact('receiving', 'logoBase64'));
            $safeNum = str_replace(['/', '\\'], '-', $receiving->document_number);
            return $pdf->stream("stock_receiving_{$safeNum}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'PDF Generation Error: ' . $e->getMessage());
        }
    }

    public function sendWhatsApp($id)
    {
        $receiving = StockReceiving::with(['vendor'])->findOrFail($id);
        $vendor = $receiving->vendor;

        if (!$vendor) {
            return back()->with('error', 'Document does not have a linked vendor.');
        }

        $phone = $vendor->mobile ?? $vendor->phone;

        if (!$phone) {
            return back()->with('error', 'Vendor does not have a mobile or phone number.');
        }

        $instanceId = SystemSetting::getValue('whatsapp_instance_id');
        $token = SystemSetting::getValue('whatsapp_token');

        if ($instanceId && $token) {
            try {
                $receiving->load(['items.product', 'warehouse', 'vendor', 'creator', 'company']);
                $logoPath = public_path('images/logo.png');
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $logoData = file_get_contents($logoPath);
                    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                }

                $pdf = Pdf::loadView('inventory.stock-receiving.pdf', compact('receiving', 'logoBase64'));
                $pdfContent = $pdf->output();

                $message = $this->whatsappService->formatDocumentMessage($receiving, 'stock_receiving');
                $fileName = "stock_receiving_" . str_replace(['/', '\\'], '-', $receiving->document_number) . ".pdf";
                $tempPath = storage_path("app/public/{$fileName}");
                file_put_contents($tempPath, $pdfContent);

                $result = $this->whatsappService->sendDocument($phone, $tempPath, $fileName, $message);

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                if (isset($result['sent']) && $result['sent']) {
                    return back()->with('success', 'WhatsApp message sent successfully!');
                } else {
                    return back()->with('error', 'WhatsApp Service Error: ' . ($result['error'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                return back()->with('error', 'WhatsApp Error: ' . $e->getMessage());
            }
        }

        // Fallback to web link
        $message = "Stock Receiving #{$receiving->document_number} from Aurex ERP";
        $url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $phone) . "?text=" . urlencode($message);
        return redirect()->away($url);
    }
}
