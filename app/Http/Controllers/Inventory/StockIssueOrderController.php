<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockIssueOrder;
use App\Models\StockIssueOrderItem;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\SystemSetting;
use App\Services\AccountingService;
use App\Services\ArabicShaper;
use App\Services\WhatsAppService;
use App\Services\StockManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockIssueOrderController extends Controller
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
        $issueOrders = StockIssueOrder::with(['warehouse', 'creator'])
            ->latest()
            ->paginate(10);
        return view('inventory.stock-issue-orders.index', compact('issueOrders'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $customers = Customer::all();
        $vendors = Vendor::all();
        return view('inventory.stock-issue-orders.create', compact('warehouses', 'customers', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'issue_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'issue_type' => 'required|in:sale,wastage,adjustment,return',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::beginTransaction();
            $documentNumber = 'SIO-' . date('Y') . '-' . str_pad(StockIssueOrder::count() + 1, 5, '0', STR_PAD_LEFT);

            $issueOrder = StockIssueOrder::create([
                'company_id' => session('active_company_id'),
                'document_number' => $documentNumber,
                'issue_date' => $request->issue_date,
                'warehouse_id' => $request->warehouse_id,
                'issue_type' => $request->issue_type,
                'customer_id' => $request->customer_id,
                'vendor_id' => $request->vendor_id,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                StockIssueOrderItem::create([
                    'stock_issue_order_id' => $issueOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.issue-orders.show', $issueOrder)
                ->with('success', __('messages.record_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $issueOrder = StockIssueOrder::with(['items.product', 'warehouse', 'customer', 'vendor', 'creator', 'poster'])->findOrFail($id);
        return view('inventory.stock-issue-orders.show', compact('issueOrder'));
    }

    public function post($id)
    {
        $issueOrder = StockIssueOrder::findOrFail($id);

        if ($issueOrder->post()) {
            return back()->with('success', __('messages.stock_issue_posted'));
        }

        return back()->with('error', 'Posting failed. Check logs for details.');
    }

    public function unpost($id)
    {
        $issueOrder = StockIssueOrder::findOrFail($id);

        if ($issueOrder->unpost()) {
            return back()->with('success', __('messages.record_unposted'));
        }

        return back()->with('error', 'Unposting failed. Check logs for details.');
    }

    public function downloadPdf($id)
    {
        try {
            $issueOrder = StockIssueOrder::with(['items.product', 'warehouse', 'company'])->findOrFail($id);

            // Base64 logo for PDF
            $logoBase64 = null;
            if ($issueOrder->company?->logo) {
                $path = public_path('storage/' . $issueOrder->company->logo);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            // Reshape Arabic text for PDF
            if ($issueOrder->company) {
                $issueOrder->company_name_ar = $this->arabicShaper->shape($issueOrder->company->name_ar ?? $issueOrder->company->name_en);
            }

            foreach ($issueOrder->items as $item) {
                $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
            }

            $pdf = Pdf::loadView('inventory.stock-issue-orders.pdf', compact('issueOrder', 'logoBase64'));
            return $pdf->stream("issue_order_{$issueOrder->document_number}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sendWhatsApp($id)
    {
        $issueOrder = StockIssueOrder::findOrFail($id);
        $message = "Stock Issue Order #{$issueOrder->document_number} ({$issueOrder->issue_type})";
        $url = "https://wa.me/?text=" . urlencode($message);
        return redirect()->away($url);
    }
}
