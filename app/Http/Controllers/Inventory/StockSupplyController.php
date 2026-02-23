<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockSupply;
use App\Models\StockSupplyItem;
use App\Models\Warehouse;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\DocumentNumber;
use App\Models\SystemSetting;
use App\Services\AccountingService;
use App\Services\StockManagementService;
use App\Services\WhatsAppService;
use App\Services\ArabicShaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class StockSupplyController extends Controller
{
    protected $accountingService;
    protected $stockService;
    protected $whatsappService;
    protected $arabicShaper;

    public function __construct(
        AccountingService $accountingService,
        StockManagementService $stockService,
        WhatsAppService $whatsappService,
        ArabicShaper $arabicShaper
    ) {
        $this->accountingService = $accountingService;
        $this->stockService = $stockService;
        $this->whatsappService = $whatsappService;
        $this->arabicShaper = $arabicShaper;
    }

    public function index()
    {
        $supplies = StockSupply::with(['warehouse', 'vendor', 'creator'])
            ->latest()
            ->paginate(10);
        return view('inventory.stock-supply.index', compact('supplies'));
    }

    public function create()
    {
        $warehouses = Warehouse::active()->get();
        $vendors = Vendor::active()->get();
        $products = Product::purchasable()->active()->get();
        $documentNumber = DocumentNumber::generate('stock_supply');

        return view('inventory.stock-supply.create', compact('warehouses', 'vendors', 'products', 'documentNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendor_id' => 'required|exists:vendors,id',
            'supply_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $supply = StockSupply::create([
                'company_id' => session('active_company_id'),
                'document_number' => DocumentNumber::generate('stock_supply'),
                'supply_date' => $request->supply_date,
                'warehouse_id' => $request->warehouse_id,
                'vendor_id' => $request->vendor_id,
                'reference_number' => $request->reference_number,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_cost'];
                StockSupplyItem::create([
                    'stock_supply_id' => $supply->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $itemTotal,
                ]);
                $totalAmount += $itemTotal;
            }

            $supply->update(['total_amount' => $totalAmount]);

            return redirect()->route('inventory.stock-supply.show', $supply)
                ->with('success', 'Stock supply created successfully.');
        });
    }

    public function show($id)
    {
        $supply = StockSupply::with(['items.product', 'warehouse', 'vendor', 'creator', 'poster'])->findOrFail($id);
        return view('inventory.stock-supply.show', compact('supply'));
    }

    public function post($id)
    {
        $supply = StockSupply::with('items')->findOrFail($id);

        if ($supply->status !== 'draft') {
            return back()->with('error', 'Only draft supplies can be posted.');
        }

        return DB::transaction(function () use ($supply) {
            // 1. Record in Stock Ledger & Update Balances
            foreach ($supply->items as $item) {
                $this->stockService->recordMovement([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $supply->warehouse_id,
                    'transaction_date' => $supply->supply_date,
                    'reference_type' => 'stock_supply',
                    'reference_id' => $supply->id,
                    'reference_number' => $supply->document_number,
                    'movement_type' => 'in',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                ]);
            }

            // 2. Post to Accounting Ledger
            if ($this->accountingService->postStockSupply($supply)) {
                $supply->update([
                    'status' => 'posted',
                    'posted_at' => now(),
                    'posted_by' => auth()->id(),
                ]);
                return back()->with('success', 'Stock supply posted successfully to stock and accounting ledgers.');
            }

            throw new \Exception('Accounting ledger posting failed.');
        });
    }

    public function downloadPdf($id)
    {
        try {
            $supply = StockSupply::with(['items.product', 'warehouse', 'vendor', 'creator', 'company'])->findOrFail($id);

            // Base64 logo for PDF
            $logoBase64 = null;
            if ($supply->company?->logo) {
                $path = public_path('storage/' . $supply->company->logo);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            // Reshape Arabic text for PDF
            if ($supply->company) {
                $supply->company_name_ar = $this->arabicShaper->shape($supply->company->name_ar ?? $supply->company->name_en);
            }
            $supply->vendor_name_ar = $this->arabicShaper->shape($supply->vendor?->name_ar ?? $supply->vendor?->name_en ?? '');
            $supply->notes_ar = $this->arabicShaper->shape($supply->notes ?? '');

            foreach ($supply->items as $item) {
                $item->product_name_ar = $this->arabicShaper->shape($item->product?->name_ar ?? $item->product?->name_en ?? '');
            }

            $pdf = Pdf::loadView('inventory.stock-supply.pdf', compact('supply', 'logoBase64'));

            $safeNum = str_replace(['/', '\\'], '-', $supply->document_number);

            return $pdf->stream("stock_supply_{$safeNum}.pdf");
        } catch (\Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function sendWhatsApp($id)
    {
        $supply = StockSupply::with(['vendor'])->findOrFail($id);
        $vendor = $supply->vendor;

        if (!$vendor) {
            return back()->with('error', 'Stock supply does not have a linked vendor.');
        }

        $phone = $vendor->mobile ?? $vendor->phone;

        if (!$phone) {
            return back()->with('error', 'Vendor does not have a mobile or phone number.');
        }

        $message = $this->whatsappService->formatDocumentMessage($supply, 'stock_supply');

        // Check if UltraMsg is configured
        $instanceId = SystemSetting::getValue('whatsapp_instance_id');
        $token = SystemSetting::getValue('whatsapp_token');

        if ($instanceId && $token) {
            try {
                // Generate PDF
                $supply->load(['items.product', 'warehouse', 'vendor', 'creator', 'company']);

                if ($supply->company) {
                    $supply->company_name_ar = $this->arabicShaper->shape($supply->company->name_ar ?? $supply->company->name_en);
                }
                $supply->vendor_name_ar = $this->arabicShaper->shape($supply->vendor?->name_ar ?? $supply->vendor?->name_en ?? '');
                $supply->notes_ar = $this->arabicShaper->shape($supply->notes ?? '');

                foreach ($supply->items as $item) {
                    $item->product_name_ar = $this->arabicShaper->shape($item->product?->name_ar ?? $item->product?->name_en ?? '');
                }

                // Base64 logo for PDF
                $logoBase64 = null;
                if ($supply->company?->logo) {
                    $path = public_path('storage/' . $supply->company->logo);
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }

                $pdf = Pdf::loadView('inventory.stock-supply.pdf', compact('supply', 'logoBase64'));
                $pdfContent = $pdf->output();

                // Save temporarily
                $fileName = "stock_supply_{$supply->document_number}.pdf";
                $tempPath = storage_path("app/public/{$fileName}");

                file_put_contents($tempPath, $pdfContent);

                // Send via API
                $result = $this->whatsappService->sendDocument($phone, $tempPath, $fileName, $message);

                // Clean up
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                if ($result['success']) {
                    return back()->with('success', 'Stock supply sent successfully via WhatsApp.');
                } else {
                    return back()->with('error', 'Failed to send WhatsApp: ' . $result['message']);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp process error for stock supply ' . $supply->document_number . ': ' . $e->getMessage());
                return back()->with('error', 'An error occurred while processing WhatsApp: ' . $e->getMessage());
            }
        }

        Log::info("UltraMsg not configured. Falling back to WhatsApp Link.");
        // Fallback to link
        $link = $this->whatsappService->generateLink($phone, $message);
        return redirect()->away($link);
    }
}
