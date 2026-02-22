<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderStatusHistory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Quotation;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\DocumentNumber;
use App\Models\AuditLog;
use App\Models\TaxSetting;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::with(['customer', 'branch', 'warehouse', 'salesman']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
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

        $salesOrders = $query->orderBy('created_at', 'desc')->paginate(20);
        $customers = Customer::active()->get();

        return view('sales.sales-orders.index', compact('salesOrders', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();
        $quotations = Quotation::where('status', 'sent')->get();

        $documentNumber = DocumentNumber::generate('sales_order');
        $selectedQuotation = null;

        // Pre-fill from quotation if provided
        if ($request->filled('quotation_id')) {
            $selectedQuotation = Quotation::with('items.product')->find($request->quotation_id);
        }

        return view('sales.sales-orders.create', compact(
            'customers',
            'branches',
            'warehouses',
            'products',
            'taxSetting',
            'documentNumber',
            'quotations',
            'selectedQuotation'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|unique:sales_orders',
            'order_number' => 'required|unique:sales_orders',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'salesman_id' => 'nullable|exists:users,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'delivery_address' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $taxSetting = TaxSetting::first();

        $salesOrder = SalesOrder::create([
            'document_number' => $validated['document_number'],
            'order_number' => $validated['order_number'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'salesman_id' => $validated['salesman_id'] ?? null,
            'quotation_id' => $validated['quotation_id'] ?? null,
            'status' => 'draft',
            'tax_rate' => $taxSetting?->default_tax_rate ?? 0,
            'shipping_amount' => $request->shipping_amount ?? 0,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            // Tax-inclusive calculation (same as invoice)
            $grossAmount = ($item['unit_price'] * $item['quantity']) * (1 - ($item['discount_percentage'] ?? 0) / 100);
            $discountAmount = ($item['unit_price'] * $item['quantity']) * (($item['discount_percentage'] ?? 0) / 100);
            $netAmount = $grossAmount / (1 + $taxRate / 100);
            $taxAmount = $grossAmount - $netAmount;

            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => round($discountAmount, 2),
                'gross_amount' => round($grossAmount, 2),
                'tax_rate' => $taxRate,
                'tax_amount' => round($taxAmount, 2),
                'net_amount' => round($netAmount, 2),
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $salesOrder->calculateTotals();

        // Update quotation status if provided
        if ($validated['quotation_id']) {
            Quotation::where('id', $validated['quotation_id'])->update([
                'status' => 'converted',
                'converted_by' => auth()->id(),
                'converted_at' => now(),
                'converted_to_id' => $salesOrder->id,
                'converted_to_type' => 'sales_order',
            ]);
        }

        AuditLog::log('create', 'sales_order', $salesOrder->id, null, $salesOrder->toArray());

        return redirect()->route('sales.sales-orders.show', $salesOrder)
            ->with('success', __('messages.sales_order_created'));
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'branch', 'warehouse', 'salesman', 'quotation', 'items.product', 'statusHistory.changer', 'salesInvoices']);
        return view('sales.sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        if (!$salesOrder->isDraft()) {
            return back()->with('error', __('messages.sales_order_not_editable'));
        }

        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();

        $salesOrder->load('items.product');

        return view('sales.sales-orders.edit', compact(
            'salesOrder',
            'customers',
            'branches',
            'warehouses',
            'products',
            'taxSetting'
        ));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if (!$salesOrder->isDraft()) {
            return back()->with('error', __('messages.sales_order_not_editable'));
        }

        $validated = $request->validate([
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'salesman_id' => 'nullable|exists:users,id',
            'delivery_address' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $oldValues = $salesOrder->toArray();

        $salesOrder->update([
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'salesman_id' => $validated['salesman_id'] ?? null,
            'shipping_amount' => $request->shipping_amount ?? 0,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $salesOrder->items()->delete();

        $taxSetting = TaxSetting::first();

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            $grossAmount = ($item['unit_price'] * $item['quantity']) * (1 - ($item['discount_percentage'] ?? 0) / 100);
            $discountAmount = ($item['unit_price'] * $item['quantity']) * (($item['discount_percentage'] ?? 0) / 100);
            $netAmount = $grossAmount / (1 + $taxRate / 100);
            $taxAmount = $grossAmount - $netAmount;

            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => round($discountAmount, 2),
                'gross_amount' => round($grossAmount, 2),
                'tax_rate' => $taxRate,
                'tax_amount' => round($taxAmount, 2),
                'net_amount' => round($netAmount, 2),
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $salesOrder->calculateTotals();

        AuditLog::log('update', 'sales_order', $salesOrder->id, $oldValues, $salesOrder->toArray());

        return redirect()->route('sales.sales-orders.show', $salesOrder)
            ->with('success', __('messages.sales_order_updated'));
    }

    public function destroy(SalesOrder $salesOrder)
    {
        if (!$salesOrder->isDraft()) {
            return back()->with('error', __('messages.sales_order_not_deletable'));
        }

        $oldValues = $salesOrder->toArray();

        $salesOrder->items()->delete();
        $salesOrder->delete();

        AuditLog::log('delete', 'sales_order', $salesOrder->id, $oldValues);

        return redirect()->route('sales.sales-orders.index')
            ->with('success', __('messages.sales_order_deleted'));
    }

    public function confirm(SalesOrder $salesOrder)
    {
        if (!$salesOrder->confirm(auth()->id())) {
            return back()->with('error', __('messages.sales_order_confirm_failed'));
        }

        AuditLog::log('confirm', 'sales_order', $salesOrder->id);

        return back()->with('success', __('messages.sales_order_confirmed'));
    }

    /**
     * Create Sales Order from Quotation
     */
    public function createFromQuotation(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
        ]);

        $quotation = Quotation::with('items.product')->findOrFail($request->quotation_id);

        if ($quotation->status !== 'sent') {
            return back()->with('error', __('messages.quotation_not_available_for_conversion'));
        }

        $salesOrder = SalesOrder::createFromQuotation($quotation, auth()->id());

        AuditLog::log('create_from_quotation', 'sales_order', $salesOrder->id, null, ['quotation_id' => $quotation->id]);

        return redirect()->route('sales.sales-orders.show', $salesOrder)
            ->with('success', __('messages.sales_order_created_from_quotation'));
    }

    /**
     * Convert Sales Order to Invoice (Full or Partial)
     */
    public function convertToInvoice(Request $request, SalesOrder $salesOrder)
    {
        if (!$salesOrder->canBeInvoiced()) {
            return back()->with('error', __('messages.sales_order_cannot_be_invoiced'));
        }

        $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:sales_order_items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        // Validate quantities don't exceed pending amounts
        $totalInvoiceAmount = 0;
        foreach ($request->items as $item) {
            $orderItem = SalesOrderItem::find($item['order_item_id']);
            $pendingQty = $orderItem->getPendingInvoiceQuantity();

            if ($item['quantity'] > $pendingQty) {
                return back()->with('error', __('messages.invoice_quantity_exceeds_pending', [
                    'product' => $orderItem->product->name,
                    'pending' => $pendingQty
                ]));
            }

            $lineNet = ($item['quantity'] * $orderItem->unit_price) *
                (1 - $orderItem->discount_percentage / 100) /
                (1 + $orderItem->tax_rate / 100);
            $totalInvoiceAmount += $lineNet;
        }

        // Create Sales Invoice
        $invoice = SalesInvoice::create([
            'document_number' => DocumentNumber::generate('sales_invoice'),
            'invoice_number' => 'INV-' . date('Y') . '-' . str_pad(SalesInvoice::count() + 1, 5, '0', STR_PAD_LEFT),
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'customer_id' => $salesOrder->customer_id,
            'branch_id' => $salesOrder->branch_id,
            'warehouse_id' => $salesOrder->warehouse_id,
            'salesman_id' => $salesOrder->salesman_id,
            'sales_order_id' => $salesOrder->id,
            'reference_type' => 'sales_order',
            'reference_id' => $salesOrder->id,
            'reference_number' => $salesOrder->document_number,
            'status' => 'draft',
            'payment_terms' => 'credit',
            'tax_rate' => $salesOrder->tax_rate,
            'notes' => 'Generated from Sales Order: ' . $salesOrder->document_number,
            'created_by' => auth()->id(),
        ]);

        // Copy selected items from sales order
        foreach ($request->items as $item) {
            $orderItem = SalesOrderItem::find($item['order_item_id']);

            $grossAmount = ($orderItem->unit_price * $item['quantity']) * (1 - $orderItem->discount_percentage / 100);
            $discountAmount = ($orderItem->unit_price * $item['quantity']) * ($orderItem->discount_percentage / 100);
            $netAmount = $grossAmount / (1 + $orderItem->tax_rate / 100);
            $taxAmount = $grossAmount - $netAmount;

            SalesInvoiceItem::create([
                'sales_invoice_id' => $invoice->id,
                'product_id' => $orderItem->product_id,
                'description' => $orderItem->description,
                'quantity' => $item['quantity'],
                'unit_price' => $orderItem->unit_price,
                'discount_percentage' => $orderItem->discount_percentage,
                'discount_amount' => round($discountAmount, 2),
                'gross_amount' => round($grossAmount, 2),
                'tax_rate' => $orderItem->tax_rate,
                'tax_amount' => round($taxAmount, 2),
                'net_amount' => round($netAmount, 2),
                'notes' => $orderItem->notes,
            ]);

            // Update invoiced quantity on order item
            $orderItem->invoiced_quantity += $item['quantity'];
            $orderItem->save();
        }

        $invoice->calculateTotals();

        // Update sales order invoiced amount and status
        $salesOrder->updateInvoicedAmount($invoice->total_amount);

        if ($request->boolean('mark_as_converted')) {
            $salesOrder->update([
                'converted_by' => auth()->id(),
                'converted_at' => now(),
            ]);
        }

        AuditLog::log('convert_to_invoice', 'sales_order', $salesOrder->id, null, ['sales_invoice_id' => $invoice->id]);

        return redirect()->route('sales.invoices.show', $invoice)
            ->with('success', __('messages.sales_order_converted_to_invoice'));
    }

    /**
     * Get pending items for partial invoice creation
     */
    public function getPendingItems(SalesOrder $salesOrder)
    {
        if (!$salesOrder->canBeInvoiced()) {
            return response()->json(['error' => 'Order cannot be invoiced'], 400);
        }

        $pendingItems = $salesOrder->items()
            ->with('product')
            ->whereRaw('invoiced_quantity < quantity')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'invoiced_quantity' => $item->invoiced_quantity,
                    'pending_quantity' => $item->getPendingInvoiceQuantity(),
                    'unit_price' => $item->unit_price,
                    'discount_percentage' => $item->discount_percentage,
                    'tax_rate' => $item->tax_rate,
                ];
            });

        return response()->json($pendingItems);
    }

    public function print(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'branch', 'warehouse', 'salesman', 'items.product']);
        return view('sales.sales-orders.print', compact('salesOrder'));
    }
}
