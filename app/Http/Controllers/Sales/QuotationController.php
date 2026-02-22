<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\AuditLog;
use App\Models\TaxSetting;
use App\Services\WhatsAppService;
use App\Services\ArabicShaper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    protected $whatsappService;
    protected $arabicShaper;

    public function __construct(WhatsAppService $whatsappService, ArabicShaper $arabicShaper)
    {
        $this->whatsappService = $whatsappService;
        $this->arabicShaper = $arabicShaper;

        $this->middleware('can:view quotations')->only(['index', 'show', 'downloadPdf', 'sendWhatsApp']);
        $this->middleware('can:create quotations')->only(['create', 'store', 'send', 'convert', 'revise']);
        $this->middleware('can:edit quotations')->only(['edit', 'update']);
        $this->middleware('can:delete quotations')->only(['destroy']);
    }
    public function index()
    {
        $quotations = Quotation::with(['customer', 'branch', 'salesman'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sales.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $salesmen = User::where('is_active', true)->get();
        $products = Product::active()->get();
        $document_number = Quotation::generateNextNumber();
        $taxSetting = TaxSetting::getCurrent();

        return view('sales.quotations.create', compact('customers', 'branches', 'warehouses', 'salesmen', 'products', 'document_number', 'taxSetting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:50|unique:quotations,document_number',
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:quotation_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'salesman_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.net_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $quotation = Quotation::create([
                'document_number' => $validated['document_number'],
                'quotation_date' => $validated['quotation_date'],
                'expiry_date' => $validated['expiry_date'],
                'customer_id' => $validated['customer_id'],
                'branch_id' => $validated['branch_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'salesman_id' => $validated['salesman_id'],
                'status' => $validated['status'],
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'terms_conditions' => $validated['terms_conditions'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_amount' => $item['net_amount'],
                    'gross_amount' => $item['net_amount'] + ($item['tax_amount'] ?? 0),
                ]);
            }

            AuditLog::log('create', 'quotation', $quotation->id, null, $quotation->load('items')->toArray());

            DB::commit();

            return redirect()->route('sales.quotations.index')
                ->with('success', __('messages.quotation_created'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.error_creating_quotation') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product']);
        return view('sales.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $salesmen = User::where('is_active', true)->get();
        $products = Product::active()->get();
        $taxSetting = TaxSetting::getCurrent();

        return view('sales.quotations.edit', compact('quotation', 'customers', 'branches', 'warehouses', 'salesmen', 'products', 'taxSetting'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:50|unique:quotations,document_number,' . $quotation->id,
            'quotation_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:quotation_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'salesman_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.net_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $quotation->load('items')->toArray();

            $quotation->update([
                'document_number' => $validated['document_number'],
                'quotation_date' => $validated['quotation_date'],
                'expiry_date' => $validated['expiry_date'],
                'customer_id' => $validated['customer_id'],
                'branch_id' => $validated['branch_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'salesman_id' => $validated['salesman_id'],
                'status' => $validated['status'],
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'terms_conditions' => $validated['terms_conditions'],
                'notes' => $validated['notes'],
            ]);

            // Sync items (delete all and recreate)
            $quotation->items()->delete();
            foreach ($validated['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_amount' => $item['net_amount'],
                    'gross_amount' => $item['net_amount'] + ($item['tax_amount'] ?? 0),
                ]);
            }

            AuditLog::log('update', 'quotation', $quotation->id, $oldValues, $quotation->load('items')->toArray());

            DB::commit();

            return redirect()->route('sales.quotations.index')
                ->with('success', __('messages.quotation_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.error_updating_quotation') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Quotation $quotation)
    {
        // Check if quotation can be deleted (e.g., if not converted to sales order)
        if ($quotation->converted_to_id) {
            return back()->with('error', __('messages.cannot_delete_converted_quotation'));
        }

        $oldValues = $quotation->load('items')->toArray();
        $quotation->items()->delete();
        $quotation->delete();

        AuditLog::log('delete', 'quotation', $quotation->id, $oldValues);

        return redirect()->route('sales.quotations.index')
            ->with('success', __('messages.quotation_deleted'));
    }

    public function downloadPdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'company']);

        // Base64 logo for PDF
        $logoBase64 = null;
        if ($quotation->company?->logo) {
            $path = public_path('storage/' . $quotation->company->logo);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // Reshape Arabic text for PDF
        if ($quotation->company) {
            $quotation->company_name_ar = $this->arabicShaper->shape($quotation->company?->name_ar ?? $quotation->company?->name_en);
        }
        $quotation->customer_name_ar = $this->arabicShaper->shape($quotation->customer?->name_ar ?? '');
        $quotation->notes_ar = $this->arabicShaper->shape($quotation->notes ?? '');

        foreach ($quotation->items as $item) {
            $item->product_name_ar = $this->arabicShaper->shape($item->product?->name_ar ?? $item->product?->name_en);
        }

        $pdf = Pdf::loadView('sales.quotations.pdf', compact('quotation', 'logoBase64'));
        return $pdf->download('quotation-' . $quotation->document_number . '.pdf');
    }

    public function sendWhatsApp(Quotation $quotation)
    {
        $customer = $quotation->customer;

        if (!$customer) {
            return back()->with('error', 'Quotation does not have a linked customer.');
        }

        $phone = $customer->mobile ?? $customer->phone;

        if (!$phone) {
            return back()->with('error', 'Customer does not have a mobile or phone number.');
        }

        $message = $this->whatsappService->formatDocumentMessage($quotation, 'quotation');
        $link = $this->whatsappService->generateLink($phone, $message);

        return redirect()->away($link);
    }

    public function print(Quotation $quotation)
    {
        $quotation->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator', 'company']);

        // Reshape Arabic text for PDF
        if ($quotation->company) {
            $quotation->company_name_ar = $this->arabicShaper->shape($quotation->company->name_ar ?? $quotation->company->name_en);
        }
        $quotation->customer_name_ar = $this->arabicShaper->shape($quotation->customer?->name_ar ?? '');
        $quotation->notes_ar = $this->arabicShaper->shape($quotation->notes ?? '');

        foreach ($quotation->items as $item) {
            $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
        }

        return view('sales.quotations.print', compact('quotation'));
    }
}
