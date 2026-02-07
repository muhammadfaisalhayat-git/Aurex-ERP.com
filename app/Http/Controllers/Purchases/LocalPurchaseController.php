<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\LocalPurchase;
use App\Models\LocalPurchaseItem;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LocalPurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:local_purchase.view')->only(['index', 'show']);
        $this->middleware('permission:local_purchase.create')->only(['create', 'store']);
        $this->middleware('permission:local_purchase.edit')->only(['edit', 'update']);
        $this->middleware('permission:local_purchase.delete')->only(['destroy']);
        $this->middleware('permission:local_purchase.post')->only(['post', 'unpost']);
    }

    public function index()
    {
        $purchases = LocalPurchase::with(['items', 'creator', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('purchases.local.index', compact('purchases'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        
        return view('purchases.local.create', compact('warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_address' => 'nullable|string',
            'warehouse_id' => 'required|exists:warehouses,id',
            'branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        
        try {
            $purchase = LocalPurchase::create([
                'document_number' => LocalPurchase::generateDocumentNumber(),
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'supplier_name' => $validated['supplier_name'],
                'supplier_phone' => $validated['supplier_phone'],
                'supplier_email' => $validated['supplier_email'],
                'supplier_address' => $validated['supplier_address'],
                'warehouse_id' => $validated['warehouse_id'],
                'branch_id' => $validated['branch_id'],
                'notes' => $validated['notes'],
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $gross = $item['quantity'] * $item['unit_price'];
                $discount = $item['discount_amount'] ?? 0;
                $grossAfterDiscount = $gross - $discount;
                $taxRate = $item['tax_rate'] ?? 0;
                $net = $grossAfterDiscount / (1 + ($taxRate / 100));
                $tax = $grossAfterDiscount - $net;

                LocalPurchaseItem::create([
                    'local_purchase_id' => $purchase->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $discount,
                    'gross_amount' => $grossAfterDiscount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $tax,
                    'net_amount' => $net,
                ]);
            }

            $purchase->recalculateTotals();

            DB::commit();

            return redirect()->route('local-purchases.show', $purchase)
                ->with('success', __('local_purchase.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(LocalPurchase $localPurchase)
    {
        $localPurchase->load(['items.item', 'creator', 'branch', 'warehouse']);
        return view('purchases.local.show', compact('localPurchase'));
    }

    public function edit(LocalPurchase $localPurchase)
    {
        if ($localPurchase->status !== 'draft') {
            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('error', __('local_purchase.cannot_edit_posted'));
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        
        return view('purchases.local.edit', compact('localPurchase', 'warehouses', 'items'));
    }

    public function update(Request $request, LocalPurchase $localPurchase)
    {
        if ($localPurchase->status !== 'draft') {
            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('error', __('local_purchase.cannot_edit_posted'));
        }

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_address' => 'nullable|string',
            'warehouse_id' => 'required|exists:warehouses,id',
            'branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        
        try {
            $localPurchase->update([
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'supplier_name' => $validated['supplier_name'],
                'supplier_phone' => $validated['supplier_phone'],
                'supplier_email' => $validated['supplier_email'],
                'supplier_address' => $validated['supplier_address'],
                'warehouse_id' => $validated['warehouse_id'],
                'branch_id' => $validated['branch_id'],
                'notes' => $validated['notes'],
            ]);

            $localPurchase->items()->delete();

            foreach ($validated['items'] as $item) {
                $gross = $item['quantity'] * $item['unit_price'];
                $discount = $item['discount_amount'] ?? 0;
                $grossAfterDiscount = $gross - $discount;
                $taxRate = $item['tax_rate'] ?? 0;
                $net = $grossAfterDiscount / (1 + ($taxRate / 100));
                $tax = $grossAfterDiscount - $net;

                LocalPurchaseItem::create([
                    'local_purchase_id' => $localPurchase->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $discount,
                    'gross_amount' => $grossAfterDiscount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $tax,
                    'net_amount' => $net,
                ]);
            }

            $localPurchase->recalculateTotals();

            DB::commit();

            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('success', __('local_purchase.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(LocalPurchase $localPurchase)
    {
        if ($localPurchase->status !== 'draft') {
            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('error', __('local_purchase.cannot_delete_posted'));
        }

        DB::beginTransaction();
        
        try {
            $localPurchase->items()->delete();
            $localPurchase->delete();
            
            DB::commit();

            return redirect()->route('local-purchases.index')
                ->with('success', __('local_purchase.deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function post(LocalPurchase $localPurchase)
    {
        if ($localPurchase->status !== 'draft') {
            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('error', __('local_purchase.already_posted'));
        }

        DB::beginTransaction();
        
        try {
            $localPurchase->post(Auth::id());
            
            DB::commit();

            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('success', __('local_purchase.posted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function unpost(LocalPurchase $localPurchase)
    {
        if ($localPurchase->status !== 'posted') {
            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('error', __('local_purchase.not_posted'));
        }

        DB::beginTransaction();
        
        try {
            $localPurchase->unpost();
            
            DB::commit();

            return redirect()->route('local-purchases.show', $localPurchase)
                ->with('success', __('local_purchase.unposted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
