<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\TaxSetting;
use App\Models\DocumentNumber;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Quotation;
use App\Services\TaxCalculator;
use App\Services\WhatsAppService;
use App\Services\ArabicShaper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SalesInvoiceController extends Controller
{
    protected $taxCalculator;
    protected $whatsappService;
    protected $arabicShaper;

    public function __construct(
        TaxCalculator $taxCalculator,
        WhatsAppService $whatsappService,
        ArabicShaper $arabicShaper
    ) {
        $this->taxCalculator = $taxCalculator;
        $this->whatsappService = $whatsappService;
        $this->arabicShaper = $arabicShaper;

        $this->middleware('can:view invoices')->only(['index', 'show', 'downloadPdf', 'print', 'getSourceDocuments', 'getSourceDocumentData', 'sendWhatsApp']);
        $this->middleware('can:create invoices')->only(['create', 'store', 'createFromQuotation', 'storeFromQuotation']);
        $this->middleware('can:edit invoices')->only(['edit', 'update']);
        $this->middleware('can:delete invoices')->only(['destroy']);
        $this->middleware('can:post invoices')->only(['post', 'unpost']);
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::with(['customer', 'branch', 'warehouse', 'salesman']);

        // Apply filters
        if ($request->filled('invoice_number')) {
            $invoiceNumber = $request->invoice_number;
            $query->where(function ($q) use ($invoiceNumber) {
                $q->where('invoice_number', 'like', "%{$invoiceNumber}%")
                    ->orWhere('document_number', 'like', "%{$invoiceNumber}%");
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('total')) {
            $query->where('total_amount', 'like', "%{$request->total}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        } elseif ($request->filled('customer_name')) {
            $customerName = $request->customer_name;
            $query->whereHas('customer', function ($q) use ($customerName) {
                $q->where('name_en', 'like', "%{$customerName}%")
                    ->orWhere('name_ar', 'like', "%{$customerName}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Global scope BelongsToTenant now handles filtering by active_company_id and active_branch_id

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        $customers = Customer::active()->get();

        return view('sales.invoices.index', compact('invoices', 'customers'));
    }


    public function getSourceDocuments(Request $request)
    {
        $type = $request->type;
        $query = $request->q;

        $results = [];

        switch ($type) {
            case 'quotation':
                $results = Quotation::where('document_number', 'LIKE', "%$query%")
                    ->orWhereHas('customer', function ($q) use ($query) {
                        $q->where('name_en', 'LIKE', "%$query%")
                            ->orWhere('name_ar', 'LIKE', "%$query%");
                    })
                    ->active()
                    ->limit(20)
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'text' => $item->document_number,
                            'customer_name' => $item->customer->name_en ?? '',
                            'date' => ($item->quotation_date instanceof \Carbon\Carbon) ? $item->quotation_date->format('Y-m-d') : ($item->quotation_date ? date('Y-m-d', strtotime($item->quotation_date)) : ''),
                            'total_amount' => $item->total_amount
                        ];
                    });
                break;
            case 'customer_request':
                $results = \App\Models\CustomerRequest::where('document_number', 'LIKE', "%$query%")
                    ->orWhereHas('customer', function ($q) use ($query) {
                        $q->where('name_en', 'LIKE', "%$query%")
                            ->orWhere('name_ar', 'LIKE', "%$query%");
                    })
                    ->limit(20)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'text' => $item->document_number,
                            'customer_name' => $item->customer->name_en ?? '',
                            'date' => ($item->request_date instanceof \Carbon\Carbon) ? $item->request_date->format('Y-m-d') : ($item->request_date ? date('Y-m-d', strtotime($item->request_date)) : ''),
                            'total_amount' => $item->total_amount
                        ];
                    });
                break;
            case 'sales_return':
                $results = \App\Models\SalesReturn::where('document_number', 'LIKE', "%$query%")
                    ->orWhereHas('customer', function ($q) use ($query) {
                        $q->where('name_en', 'LIKE', "%$query%");
                    })
                    ->limit(20)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'text' => $item->document_number,
                            'customer_name' => $item->customer->name_en ?? '',
                            'date' => ($item->return_date instanceof \Carbon\Carbon) ? $item->return_date->format('Y-m-d') : ($item->return_date ? date('Y-m-d', strtotime($item->return_date)) : ''),
                            'total_amount' => $item->total_amount
                        ];
                    });
                break;
            case 'sales_order':
                $results = \App\Models\SalesOrder::where('document_number', 'LIKE', "%$query%")
                    ->orWhereHas('customer', function ($q) use ($query) {
                        $q->where('name_en', 'LIKE', "%$query%");
                    })
                    ->limit(20)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'text' => $item->document_number,
                            'customer_name' => $item->customer->name_en ?? '',
                            'date' => ($item->order_date instanceof \Carbon\Carbon) ? $item->order_date->format('Y-m-d') : ($item->order_date ? date('Y-m-d', strtotime($item->order_date)) : ''),
                            'total_amount' => $item->total_amount
                        ];
                    });
                break;
        }

        return response()->json($results);
    }

    public function getSourceDocumentData($type, $id)
    {
        $data = null;
        switch ($type) {
            case 'quotation':
                $data = Quotation::with('items.product')->find($id);
                break;
            case 'customer_request':
                $data = \App\Models\CustomerRequest::with('items.product')->find($id);
                break;
            case 'sales_return':
                $data = \App\Models\SalesReturn::with('items.product')->find($id);
                break;
            case 'sales_order':
                $data = \App\Models\SalesOrder::with('items.product')->find($id);
                break;
            case 'sales_invoice':
                $data = \App\Models\SalesInvoice::with('items.product', 'customer')->find($id);
                break;
        }

        if (!$data) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Normalize items
        $normalizedItems = $data->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name ?? 'Unknown',
                'product_code' => $item->product->product_code ?? '',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_percentage' => $item->discount_percentage ?? 0,
                'tax_rate' => $item->tax_rate ?? 0,
            ];
        });

        return response()->json([
            'customer_id' => $data->customer_id,
            'customer_name' => $data->customer->name_en ?? '',
            'branch_id' => $data->branch_id,
            'warehouse_id' => $data->warehouse_id,
            'salesman_id' => $data->salesman_id ?? null,
            'items' => $normalizedItems,
        ]);
    }


    public function create()
    {
        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();

        // Global scope BelongsToTenant now handles filtering

        $documentNumber = DocumentNumber::generate('sales_invoice');
        $salesmen = User::active()->get();

        return view('sales.invoices.create', compact(
            'customers',
            'branches',
            'warehouses',
            'products',
            'taxSetting',
            'documentNumber',
            'salesmen'
        ));
    }

    public function store(Request $request)
    {
        // If document_number is not sent, use invoice_number
        if (!$request->has('document_number') && $request->has('invoice_number')) {
            $request->merge(['document_number' => $request->invoice_number]);
        }

        $validated = $request->validate([
            'document_number' => 'required|unique:sales_invoices',
            'invoice_number' => 'required|unique:sales_invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'customer_id' => 'nullable|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'salesman_id' => 'nullable|exists:users,id',
            'reference_number' => 'nullable|string',
            'payment_terms' => 'required|in:cash,credit,installment',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string',
        ]);

        $taxSetting = TaxSetting::first();

        // Create invoice
        $invoice = SalesInvoice::create([
            'document_number' => $validated['document_number'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'customer_id' => $validated['customer_id'] ?? null,
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'salesman_id' => $validated['salesman_id'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'payment_terms' => $validated['payment_terms'],
            'status' => 'draft',
            'tax_rate' => $taxSetting?->default_tax_rate ?? 0,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Create items
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            $lineTotals = SalesInvoiceItem::calculateLineTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percentage'] ?? 0,
                $taxRate
            );

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => $lineTotals['discount_amount'],
                'gross_amount' => $lineTotals['gross_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTotals['tax_amount'],
                'net_amount' => $lineTotals['net_amount'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        // Calculate totals
        $invoice->calculateTotals();

        // Log audit
        AuditLog::log('create', 'sales_invoice', $invoice->id, null, $invoice->toArray());

        return redirect()->route('sales.invoices.show', $invoice)
            ->with('success', __('messages.invoice_created'));
    }

    public function show(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator', 'poster']);
        return view('sales.invoices.show', compact('invoice'));
    }

    public function edit(SalesInvoice $invoice)
    {
        if (!$invoice->isEditable()) {
            return back()->with('error', __('messages.invoice_not_editable'));
        }

        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();

        $invoice->load('items.product');

        return view('sales.invoices.edit', compact(
            'invoice',
            'customers',
            'branches',
            'warehouses',
            'products',
            'taxSetting'
        ));
    }

    public function update(Request $request, SalesInvoice $invoice)
    {
        if (!$invoice->isEditable()) {
            return back()->with('error', __('messages.invoice_not_editable'));
        }

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'salesman_id' => 'nullable|exists:users,id',
            'reference_number' => 'nullable|string',
            'payment_terms' => 'required|in:cash,credit,installment',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:sales_invoice_items',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string',
        ]);

        $oldValues = $invoice->toArray();

        $invoice->update([
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'salesman_id' => $validated['salesman_id'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'payment_terms' => $validated['payment_terms'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Delete existing items
        $invoice->items()->delete();

        $taxSetting = TaxSetting::first();

        // Recreate items
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            $lineTotals = SalesInvoiceItem::calculateLineTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percentage'] ?? 0,
                $taxRate
            );

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => $lineTotals['discount_amount'],
                'gross_amount' => $lineTotals['gross_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTotals['tax_amount'],
                'net_amount' => $lineTotals['net_amount'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        // Recalculate totals
        $invoice->calculateTotals();

        // Log audit
        AuditLog::log('update', 'sales_invoice', $invoice->id, $oldValues, $invoice->toArray());

        return redirect()->route('sales.invoices.show', $invoice)
            ->with('success', __('messages.invoice_updated'));
    }

    public function destroy(SalesInvoice $invoice)
    {
        if (!$invoice->isEditable()) {
            return back()->with('error', __('messages.invoice_not_deletable'));
        }

        $oldValues = $invoice->toArray();

        $invoice->items()->delete();
        $invoice->delete();

        // Log audit
        AuditLog::log('delete', 'sales_invoice', $invoice->id, $oldValues);

        return redirect()->route('sales.invoices.index')
            ->with('success', __('messages.invoice_deleted'));
    }

    public function post(SalesInvoice $invoice)
    {
        if (!auth()->user()->hasPermissionTo('post invoices')) {
            return back()->with('error', __('messages.no_permission_to_post'));
        }

        if ($invoice->post()) {
            // Log audit
            AuditLog::log('post', 'sales_invoice', $invoice->id);

            return back()->with('success', __('messages.invoice_posted'));
        }

        return back()->with('error', __('messages.invoice_post_failed'));
    }

    public function unpost(SalesInvoice $invoice)
    {
        if (!auth()->user()->hasPermissionTo('post invoices')) {
            return back()->with('error', __('messages.no_permission_to_unpost'));
        }

        if ($invoice->unpost()) {
            // Log audit
            AuditLog::log('unpost', 'sales_invoice', $invoice->id);

            return back()->with('success', __('messages.invoice_unposted'));
        }

        return back()->with('error', __('messages.invoice_unpost_failed'));
    }

    public function downloadPdf(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator', 'company']);

        // Base64 logo for PDF
        $logoBase64 = null;
        if ($invoice->company?->logo) {
            $path = public_path('storage/' . $invoice->company->logo);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // Reshape Arabic text for PDF
        if ($invoice->company) {
            $invoice->company_name_ar = $this->arabicShaper->shape($invoice->company->name_ar ?? $invoice->company->name_en);
        }
        $invoice->customer_name_ar = $this->arabicShaper->shape($invoice->customer?->name_ar ?? '');
        $invoice->notes_ar = $this->arabicShaper->shape($invoice->notes ?? '');

        foreach ($invoice->items as $item) {
            $item->product_name_ar = $this->arabicShaper->shape($item->product?->name_ar ?? $item->product?->name_en);
            if ($item->description) {
                $item->description_ar = $this->arabicShaper->shape($item->description);
            }
        }

        $pdf = PDF::loadView('sales.invoices.pdf', compact('invoice', 'logoBase64'));

        return $pdf->download("invoice_{$invoice->document_number}.pdf");
    }

    public function print(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator', 'company']);

        // Reshape Arabic text for PDF
        if ($invoice->company) {
            $invoice->company_name_ar = $this->arabicShaper->shape($invoice->company->name_ar ?? $invoice->company->name_en);
        }
        $invoice->customer_name_ar = $this->arabicShaper->shape($invoice->customer?->name_ar ?? '');
        $invoice->notes_ar = $this->arabicShaper->shape($invoice->notes ?? '');

        foreach ($invoice->items as $item) {
            $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
            if ($item->description) {
                $item->description_ar = $this->arabicShaper->shape($item->description);
            }
        }

        return view('sales.invoices.print', compact('invoice'));
    }

    /**
     * Create Invoice directly from Quotation
     */
    public function createFromQuotation(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['sent', 'accepted'])) {
            return back()->with('error', __('messages.quotation_not_available_for_conversion'));
        }

        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();

        $quotation->load('items.product');

        $documentNumber = DocumentNumber::generate('sales_invoice');
        $salesmen = User::active()->get();

        return view('sales.invoices.create-from-quotation', compact(
            'quotation',
            'customers',
            'branches',
            'warehouses',
            'products',
            'taxSetting',
            'documentNumber',
            'salesmen'
        ));
    }

    /**
     * Store Invoice created from Quotation
     */
    public function storeFromQuotation(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['sent', 'accepted'])) {
            return back()->with('error', __('messages.quotation_not_available_for_conversion'));
        }

        $validated = $request->validate([
            'document_number' => 'required|unique:sales_invoices',
            'invoice_number' => 'required|unique:sales_invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'payment_terms' => 'required|in:cash,credit,installment',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $taxSetting = TaxSetting::first();

        // Create invoice
        $invoice = SalesInvoice::create([
            'document_number' => $validated['document_number'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'customer_id' => $quotation->customer_id,
            'branch_id' => $quotation->branch_id,
            'warehouse_id' => $quotation->warehouse_id,
            'salesman_id' => $quotation->salesman_id,
            'quotation_id' => $quotation->id,
            'reference_type' => 'quotation',
            'reference_id' => $quotation->id,
            'reference_number' => $quotation->document_number,
            'payment_terms' => $validated['payment_terms'],
            'status' => 'draft',
            'tax_rate' => $quotation->tax_rate,
            'notes' => $validated['notes'] ?? 'Generated from Quotation: ' . $quotation->document_number,
            'created_by' => auth()->id(),
        ]);

        // Create items from quotation items
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            $lineTotals = SalesInvoiceItem::calculateLineTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percentage'] ?? 0,
                $taxRate
            );

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => $lineTotals['discount_amount'],
                'gross_amount' => $lineTotals['gross_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTotals['tax_amount'],
                'net_amount' => $lineTotals['net_amount'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $invoice->calculateTotals();

        // Update quotation status
        $quotation->update([
            'status' => 'converted',
            'converted_by' => auth()->id(),
            'converted_at' => now(),
            'converted_to_id' => $invoice->id,
            'converted_to_type' => 'sales_invoice',
        ]);

        AuditLog::log('create_from_quotation', 'sales_invoice', $invoice->id, null, ['quotation_id' => $quotation->id]);

        return redirect()->route('sales.invoices.show', $invoice)
            ->with('success', __('messages.invoice_created_from_quotation'));
    }

    public function sendWhatsApp(SalesInvoice $invoice)
    {
        $customer = $invoice->customer;

        if (!$customer) {
            return back()->with('error', 'Invoice does not have a linked customer.');
        }

        $phone = $customer->mobile ?? $customer->phone;

        if (!$phone) {
            return back()->with('error', 'Customer does not have a mobile or phone number.');
        }

        $message = $this->whatsappService->formatDocumentMessage($invoice, 'invoice');

        // Check if UltraMsg is configured
        $instanceId = \App\Models\SystemSetting::getValue('whatsapp_instance_id');
        $token = \App\Models\SystemSetting::getValue('whatsapp_token');

        Log::info('WhatsApp config check in SalesInvoiceController', [
            'instance_id_configured' => !empty($instanceId),
            'token_configured' => !empty($token),
            'document_number' => $invoice->document_number
        ]);

        if ($instanceId && $token) {
            Log::info('WhatsApp processing started for invoice: ' . $invoice->document_number);

            try {
                // Generate PDF
                $invoice->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator', 'company']);

                // Reshape Arabic text for PDF
                if ($invoice->company) {
                    $invoice->company_name_ar = $this->arabicShaper->shape($invoice->company->name_ar ?? $invoice->company->name_en);
                }
                $invoice->customer_name_ar = $this->arabicShaper->shape($invoice->customer->name_ar ?? '');
                $invoice->notes_ar = $this->arabicShaper->shape($invoice->notes ?? '');

                foreach ($invoice->items as $item) {
                    $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
                    if ($item->description) {
                        $item->description_ar = $this->arabicShaper->shape($item->description);
                    }
                }

                $pdf = PDF::loadView('sales.invoices.pdf', compact('invoice'));
                $pdfContent = $pdf->output();

                if (empty($pdfContent)) {
                    Log::error('WhatsApp send error: PDF content is empty for invoice ' . $invoice->document_number);
                    return back()->with('error', 'Failed to generate PDF content.');
                }

                // Save temporarily
                $fileName = "invoice_{$invoice->document_number}.pdf";
                $tempPath = storage_path("app/public/{$fileName}");

                if (file_put_contents($tempPath, $pdfContent) === false) {
                    Log::error('WhatsApp send error: Failed to write temporary PDF to ' . $tempPath);
                    return back()->with('error', 'Failed to save temporary PDF file.');
                }

                Log::info('Temporary PDF created successfully: ' . $tempPath);

                // Send via API
                $result = $this->whatsappService->sendDocument($phone, $tempPath, $fileName, $message);

                // Clean up
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                    Log::info('Temporary PDF cleaned up: ' . $tempPath);
                }

                if ($result['success']) {
                    Log::info('Invoice sent successfully via WhatsApp API: ' . $invoice->document_number);
                    return back()->with('success', 'Invoice sent successfully via WhatsApp.');
                } else {
                    Log::error('Invoice WhatsApp API send failed: ' . $invoice->document_number . ' - ' . $result['message']);
                    return back()->with('error', 'Failed to send WhatsApp: ' . $result['message']);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp process error for invoice ' . $invoice->document_number . ': ' . $e->getMessage());
                return back()->with('error', 'An error occurred while processing WhatsApp: ' . $e->getMessage());
            }
        }

        // Fallback to link if not configured
        $link = $this->whatsappService->generateLink($phone, $message);
        return redirect()->away($link);
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $invoices = SalesInvoice::where('document_number', 'like', "%$search%")
            ->limit(10)
            ->get(['id', 'document_number']);

        return response()->json($invoices);
    }
}
