<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplyOrder;
use App\Models\SupplyOrderItem;
use App\Models\SupplyOrderStatusHistory;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\DocumentNumber;
use App\Models\AuditLog;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\TaxSetting;

class SupplyOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplyOrder::with(['vendor', 'branch', 'warehouse']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vq) use ($search) {
                        $vq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $supplyOrders = $query->orderBy('created_at', 'desc')->paginate(20);
        $vendors = Vendor::active()->get();

        return view('purchases.supply-orders.index', compact('supplyOrders', 'vendors'));
    }

    public function create()
    {
        $vendors = Vendor::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::purchasable()->active()->get();
        $taxSetting = TaxSetting::first();

        $documentNumber = DocumentNumber::generate('supply_order');

        return view('purchases.supply-orders.create', compact(
            'vendors',
            'branches',
            'warehouses',
            'products',
            'taxSetting',
            'documentNumber'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|unique:supply_orders',
            'order_number' => 'required|unique:supply_orders',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'vendor_id' => 'required|exists:vendors,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $taxSetting = TaxSetting::first();

        $supplyOrder = SupplyOrder::create([
            'document_number' => $validated['document_number'],
            'order_number' => $validated['order_number'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'vendor_id' => $validated['vendor_id'],
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'status' => 'draft',
            'tax_rate' => $taxSetting?->default_tax_rate ?? 0,
            'shipping_amount' => $request->shipping_amount ?? 0,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            $lineTotals = SupplyOrderItem::calculateLineTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percentage'] ?? 0,
                $taxRate
            );

            $supplyOrder->items()->create([
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => $lineTotals['discount_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTotals['tax_amount'],
                'total_amount' => $lineTotals['total_amount'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $supplyOrder->calculateTotals();

        AuditLog::log('create', 'supply_order', $supplyOrder->id, null, $supplyOrder->toArray());

        return redirect()->route('purchases.supply-orders.show', $supplyOrder)
            ->with('success', __('messages.supply_order_created'));
    }

    public function show(SupplyOrder $supplyOrder)
    {
        $supplyOrder->load([
            'vendor',
            'branch' => function ($query) {
                $query->withoutGlobalScope('tenant');
            },
            'warehouse' => function ($query) {
                $query->withoutGlobalScope('tenant');
            },
            'items.product' => function ($query) {
                $query->withoutGlobalScope('tenant');
            },
            'creator',
            'statusHistory.changer'
        ]);
        return view('purchases.supply-orders.show', compact('supplyOrder'));
    }

    public function edit(SupplyOrder $supplyOrder)
    {
        if (!$supplyOrder->isDraft()) {
            return back()->with('error', __('messages.supply_order_not_editable'));
        }

        $vendors = Vendor::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::purchasable()->active()->get();
        $taxSetting = TaxSetting::first();

        $supplyOrder->load('items.product');

        return view('purchases.supply-orders.edit', compact(
            'supplyOrder',
            'vendors',
            'branches',
            'warehouses',
            'products',
            'taxSetting'
        ));
    }

    public function update(Request $request, SupplyOrder $supplyOrder)
    {
        if (!$supplyOrder->isDraft()) {
            return back()->with('error', __('messages.supply_order_not_editable'));
        }

        $validated = $request->validate([
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'vendor_id' => 'required|exists:vendors,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $oldValues = $supplyOrder->toArray();

        $supplyOrder->update([
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'vendor_id' => $validated['vendor_id'],
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'shipping_amount' => $request->shipping_amount ?? 0,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $supplyOrder->items()->delete();

        $taxSetting = TaxSetting::first();

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $taxRate = $product->tax_rate ?? $taxSetting?->default_tax_rate ?? 0;

            $lineTotals = SupplyOrderItem::calculateLineTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['discount_percentage'] ?? 0,
                $taxRate
            );

            $supplyOrder->items()->create([
                'product_id' => $item['product_id'],
                'description' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => $lineTotals['discount_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTotals['tax_amount'],
                'total_amount' => $lineTotals['total_amount'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $supplyOrder->calculateTotals();

        AuditLog::log('update', 'supply_order', $supplyOrder->id, $oldValues, $supplyOrder->toArray());

        return redirect()->route('purchases.supply-orders.show', $supplyOrder)
            ->with('success', __('messages.supply_order_updated'));
    }

    public function destroy(SupplyOrder $supplyOrder)
    {
        if (!$supplyOrder->isDraft()) {
            return back()->with('error', __('messages.supply_order_not_deletable'));
        }

        $oldValues = $supplyOrder->toArray();

        $supplyOrder->items()->delete();
        $supplyOrder->delete();

        AuditLog::log('delete', 'supply_order', $supplyOrder->id, $oldValues);

        return redirect()->route('purchases.supply-orders.index')
            ->with('success', __('messages.supply_order_deleted'));
    }

    public function send(SupplyOrder $supplyOrder)
    {
        if (!$supplyOrder->isDraft()) {
            return back()->with('error', __('messages.supply_order_already_sent'));
        }

        $supplyOrder->markAsSent(auth()->id());

        AuditLog::log('send', 'supply_order', $supplyOrder->id);

        return back()->with('success', __('messages.supply_order_sent'));
    }

    /**
     * Convert Supply Order to Purchase Invoice
     */
    public function convertToInvoice(Request $request, SupplyOrder $supplyOrder)
    {
        if (!$supplyOrder->canBeInvoiced()) {
            return back()->with('error', __('messages.supply_order_cannot_be_invoiced'));
        }

        $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
        ]);

        // Create Purchase Invoice
        $invoice = PurchaseInvoice::create([
            'document_number' => DocumentNumber::generate('purchase_invoice'),
            'invoice_number' => 'PINV-' . date('Y') . '-' . str_pad(PurchaseInvoice::count() + 1, 5, '0', STR_PAD_LEFT),
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'vendor_id' => $supplyOrder->vendor_id,
            'branch_id' => $supplyOrder->branch_id,
            'warehouse_id' => $supplyOrder->warehouse_id,
            'purchase_order_number' => $supplyOrder->order_number,
            'reference_type' => 'supply_order',
            'reference_id' => $supplyOrder->id,
            'reference_number' => $supplyOrder->document_number,
            'status' => 'draft',
            'payment_terms' => 'credit',
            'subtotal' => $supplyOrder->subtotal,
            'discount_amount' => $supplyOrder->discount_amount,
            'tax_rate' => $supplyOrder->tax_rate,
            'tax_amount' => $supplyOrder->tax_amount,
            'shipping_amount' => $supplyOrder->shipping_amount,
            'total_amount' => $supplyOrder->total_amount,
            'balance_amount' => $supplyOrder->total_amount,
            'notes' => 'Generated from Supply Order: ' . $supplyOrder->document_number,
            'created_by' => auth()->id(),
        ]);

        // Copy items from supply order
        foreach ($supplyOrder->items as $orderItem) {
            PurchaseInvoiceItem::create([
                'purchase_invoice_id' => $invoice->id,
                'product_id' => $orderItem->product_id,
                'description' => $orderItem->description,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->unit_price,
                'discount_percentage' => $orderItem->discount_percentage,
                'discount_amount' => $orderItem->discount_amount,
                'tax_rate' => $orderItem->tax_rate,
                'tax_amount' => $orderItem->tax_amount,
                'total_amount' => $orderItem->total_amount,
                'notes' => $orderItem->notes,
            ]);
        }

        // Update supply order
        $supplyOrder->convertToInvoice(auth()->id());
        $supplyOrder->update(['purchase_invoice_id' => $invoice->id]);

        AuditLog::log('convert_to_invoice', 'supply_order', $supplyOrder->id, null, ['purchase_invoice_id' => $invoice->id]);

        return redirect()->route('purchases.invoices.show', $invoice)
            ->with('success', __('messages.supply_order_converted_to_invoice'));
    }

    public function print(SupplyOrder $supplyOrder)
    {
        $supplyOrder->load([
            'vendor',
            'branch' => function ($query) {
                $query->withoutGlobalScope('tenant');
            },
            'warehouse' => function ($query) {
                $query->withoutGlobalScope('tenant');
            },
            'items.product' => function ($query) {
                $query->withoutGlobalScope('tenant');
            }
        ]);
        return view('purchases.supply-orders.print', compact('supplyOrder'));
    }
}
