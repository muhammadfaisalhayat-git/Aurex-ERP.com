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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
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

        return view('sales.quotations.create', compact('customers', 'branches', 'warehouses', 'salesmen', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:50|unique:quotations,document_number',
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:quotation_date',
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

            AuditLog::create([
                'action' => 'create',
                'entity_type' => 'quotation',
                'entity_id' => $quotation->id,
                'user_id' => auth()->id(),
                'new_values' => $quotation->load('items')->toArray(),
            ]);

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

        return view('sales.quotations.edit', compact('quotation', 'customers', 'branches', 'warehouses', 'salesmen', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:50|unique:quotations,document_number,' . $quotation->id,
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:quotation_date',
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

            AuditLog::create([
                'action' => 'update',
                'entity_type' => 'quotation',
                'entity_id' => $quotation->id,
                'user_id' => auth()->id(),
                'old_values' => $oldValues,
                'new_values' => $quotation->load('items')->toArray(),
            ]);

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

        AuditLog::create([
            'action' => 'delete',
            'entity_type' => 'quotation',
            'entity_id' => $quotation->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
        ]);

        return redirect()->route('sales.quotations.index')
            ->with('success', __('messages.quotation_deleted'));
    }
}
