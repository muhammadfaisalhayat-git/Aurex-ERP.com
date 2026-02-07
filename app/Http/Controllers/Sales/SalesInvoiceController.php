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
use App\Services\TaxCalculator;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesInvoiceController extends Controller
{
    protected $taxCalculator;

    public function __construct(TaxCalculator $taxCalculator)
    {
        $this->taxCalculator = $taxCalculator;
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::with(['customer', 'branch', 'warehouse', 'salesman']);

        // Apply filters
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Branch filter for non-super-admin
        if (!auth()->user()->isSuperAdmin()) {
            $userBranchIds = auth()->user()->branch_id ? [auth()->user()->branch_id] : [];
            $userWarehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
            
            if (!empty($userBranchIds)) {
                $query->whereIn('branch_id', $userBranchIds);
            }
            if (!empty($userWarehouseIds)) {
                $query->whereIn('warehouse_id', $userWarehouseIds);
            }
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        $customers = Customer::active()->get();

        return view('sales.invoices.index', compact('invoices', 'customers'));
    }

    public function create()
    {
        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();

        // Filter by user permissions
        if (!auth()->user()->isSuperAdmin()) {
            if (auth()->user()->branch_id) {
                $branches = $branches->where('id', auth()->user()->branch_id);
            }
            $userWarehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
            $warehouses = $warehouses->whereIn('id', $userWarehouseIds);
        }

        $documentNumber = DocumentNumber::generate('sales_invoice');

        return view('sales.invoices.create', compact(
            'customers', 'branches', 'warehouses', 'products', 
            'taxSetting', 'documentNumber'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|unique:sales_invoices',
            'invoice_number' => 'required|unique:sales_invoices',
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
            'customer_id' => $validated['customer_id'],
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
        AuditLog::create([
            'action' => 'create',
            'entity_type' => 'sales_invoice',
            'entity_id' => $invoice->id,
            'user_id' => auth()->id(),
            'new_values' => $invoice->toArray(),
        ]);

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
            'invoice', 'customers', 'branches', 'warehouses', 
            'products', 'taxSetting'
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
        AuditLog::create([
            'action' => 'update',
            'entity_type' => 'sales_invoice',
            'entity_id' => $invoice->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
            'new_values' => $invoice->toArray(),
        ]);

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
        AuditLog::create([
            'action' => 'delete',
            'entity_type' => 'sales_invoice',
            'entity_id' => $invoice->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
        ]);

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
            AuditLog::create([
                'action' => 'post',
                'entity_type' => 'sales_invoice',
                'entity_id' => $invoice->id,
                'user_id' => auth()->id(),
            ]);

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
            AuditLog::create([
                'action' => 'unpost',
                'entity_type' => 'sales_invoice',
                'entity_id' => $invoice->id,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', __('messages.invoice_unposted'));
        }

        return back()->with('error', __('messages.invoice_unpost_failed'));
    }

    public function downloadPdf(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator']);
        
        $pdf = PDF::loadView('sales.invoices.pdf', compact('invoice'));
        
        return $pdf->download("invoice_{$invoice->document_number}.pdf");
    }

    public function print(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product', 'creator']);
        
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

        return view('sales.invoices.create-from-quotation', compact(
            'quotation', 'customers', 'branches', 'warehouses', 
            'products', 'taxSetting', 'documentNumber'
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

        AuditLog::create([
            'action' => 'create_from_quotation',
            'entity_type' => 'sales_invoice',
            'entity_id' => $invoice->id,
            'user_id' => auth()->id(),
            'new_values' => ['quotation_id' => $quotation->id],
        ]);

        return redirect()->route('sales.invoices.show', $invoice)
            ->with('success', __('messages.invoice_created_from_quotation'));
    }
}
