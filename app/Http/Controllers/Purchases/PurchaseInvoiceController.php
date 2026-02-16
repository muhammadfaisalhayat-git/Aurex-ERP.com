<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\AuditLog;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PurchaseInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view purchase invoices')->only(['index', 'show', 'print']);
        $this->middleware('can:create purchase invoices')->only(['create', 'store']);
        $this->middleware('can:edit purchase invoices')->only(['edit', 'update']);
        $this->middleware('can:delete purchase invoices')->only(['destroy']);
        $this->middleware('can:post purchase invoices')->only(['post']);
        $this->middleware('can:unpost purchase invoices')->only(['unpost']);
    }

    public function index(Request $request)
    {
        $query = PurchaseInvoice::with(['vendor', 'branch']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vq) use ($search) {
                        $vq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(10);

        return view('purchases.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $vendors = Vendor::active()->get();
        $branches = Branch::active()->get();
        $nextDocumentNumber = PurchaseInvoice::generateNextDocumentNumber();
        return view('purchases.invoices.create', compact('vendors', 'branches', 'nextDocumentNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'vendor_id' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_order_number' => 'nullable|string|max:50',
            'payment_terms' => 'required|in:cash,credit,installment',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
        ]);

        // Default due_date to invoice_date if missing
        if (empty($validated['due_date'])) {
            $validated['due_date'] = $validated['invoice_date'];
        }

        try {
            DB::beginTransaction();

            // Handle New Vendor
            if (str_starts_with($request->vendor_id, 'new:')) {
                $vendorName = str_replace('new:', '', $request->vendor_id);
                $vendor = Vendor::create([
                    'name_en' => $vendorName,
                    'name_ar' => $vendorName,
                    'code' => Vendor::generateNextCode(),
                    'branch_id' => $request->branch_id,
                    'tax_number' => $request->vendor_tax_id,
                    'address' => $request->vendor_address,
                    'status' => 'active',
                ]);
                $validated['vendor_id'] = $vendor->id;
            } else {
                // Confirm valid ID if not new
                $vendor = Vendor::findOrFail($request->vendor_id);
                $validated['vendor_id'] = $vendor->id;
            }

            // Handle OCR Document Persistence
            if ($request->filled('ocr_temp_file')) {
                $tempPath = config('ocr.upload_path') . DIRECTORY_SEPARATOR . $request->ocr_temp_file;
                if (File::exists($tempPath)) {
                    $newPath = 'vendors/documents/' . $vendor->id . '_' . time() . '_' . $request->ocr_temp_file;
                    Storage::disk('public')->put($newPath, File::get($tempPath));

                    VendorDocument::create([
                        'vendor_id' => $vendor->id,
                        'document_type' => 'invoice',
                        'file_path' => $newPath,
                        'original_filename' => $request->ocr_temp_file,
                        'notes' => 'Auto-uploaded via OCR on ' . now()->format('Y-m-d'),
                        'uploaded_by' => auth()->id(),
                    ]);

                    // Clean up temp
                    File::delete($tempPath);
                }
            }

            $validated['document_number'] = PurchaseInvoice::generateNextDocumentNumber();
            $validated['status'] = 'draft';
            $validated['created_by'] = auth()->id();

            // Pre-process items to handle new products
            $items = [];
            $subtotal = 0;
            $totalTax = 0;

            // Get or create a default category for OCR products
            $defaultCategory = ProductCategory::where('name_en', 'General')
                ->orWhere('name_ar', 'عام')
                ->first();

            if (!$defaultCategory) {
                $defaultCategory = ProductCategory::create([
                    'name_en' => 'General',
                    'name_ar' => 'عام',
                    'code' => 'GEN',
                    'is_active' => true
                ]);
            }

            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                if (str_starts_with($productId, 'new:')) {
                    $productName = str_replace('new:', '', $productId);
                    $product = Product::create([
                        'name_en' => $productName,
                        'name_ar' => $productName,
                        'category_id' => $defaultCategory->id,
                        'code' => 'PRD-' . strtoupper(uniqid()),
                        'branch_id' => $request->branch_id,
                        'is_active' => true,
                        'is_purchasable' => true,
                        'is_sellable' => true,
                        'cost_price' => $item['unit_price'],
                        'tax_rate' => $item['tax_rate'] ?? 0,
                    ]);
                    $productId = $product->id;
                } else {
                    if (!Product::where('id', $productId)->exists()) {
                        throw new \Exception(__('messages.invalid_product') . ': ' . $productId);
                    }
                }

                $lineSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineSubtotal;
                $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $totalTax += $lineTax;

                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'total_amount' => $lineSubtotal + $lineTax,
                ];
            }

            $validated['subtotal'] = $subtotal;
            $validated['tax_amount'] = $totalTax;
            $validated['total_amount'] = $subtotal + $totalTax + ($request->shipping_amount ?? 0) - ($request->discount_amount ?? 0);
            $validated['balance_amount'] = $validated['total_amount'];

            $invoice = PurchaseInvoice::create($validated);

            foreach ($items as $itemData) {
                $invoice->items()->create($itemData);
            }

            AuditLog::create([
                'action' => 'create',
                'entity_type' => 'purchase_invoice',
                'entity_id' => $invoice->id,
                'user_id' => auth()->id(),
                'new_values' => $invoice->toArray(),
            ]);

            DB::commit();

            return redirect()->route('purchases.invoices.index')
                ->with('success', __('messages.invoice_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'branch', 'warehouse', 'items.product', 'creator'])->findOrFail($id);
        return view('purchases.invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = PurchaseInvoice::with('items')->findOrFail($id);
        if ($invoice->isPosted()) {
            return back()->with('error', __('messages.invoice_not_editable'));
        }
        $vendors = Vendor::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::where('branch_id', $invoice->branch_id)->active()->get();
        return view('purchases.invoices.edit', compact('invoice', 'vendors', 'branches', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        if ($invoice->isPosted()) {
            return back()->with('error', __('messages.invoice_not_editable'));
        }

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'vendor_id' => 'required|exists:vendors,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_order_number' => 'nullable|string|max:50',
            'payment_terms' => 'required|in:cash,credit,installment',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $invoice->toArray();

            // Calculate totals
            $subtotal = 0;
            $totalTax = 0;
            foreach ($request->items as $item) {
                $lineSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineSubtotal;
                $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $totalTax += $lineTax;
            }

            $validated['subtotal'] = $subtotal;
            $validated['tax_amount'] = $totalTax;
            $validated['total_amount'] = $subtotal + $totalTax + ($request->shipping_amount ?? 0) - ($request->discount_amount ?? 0);
            $validated['balance_amount'] = $validated['total_amount'] - $invoice->paid_amount;

            $invoice->update($validated);

            // Update items
            $invoice->items()->delete();
            foreach ($request->items as $item) {
                $lineSubtotal = $item['quantity'] * $item['unit_price'];
                $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 0) / 100);

                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total_amount' => $lineSubtotal + $lineTax,
                ]);
            }

            AuditLog::create([
                'action' => 'update',
                'entity_type' => 'purchase_invoice',
                'entity_id' => $invoice->id,
                'user_id' => auth()->id(),
                'old_values' => $oldValues,
                'new_values' => $invoice->toArray(),
            ]);

            DB::commit();

            return redirect()->route('purchases.invoices.index')
                ->with('success', __('messages.invoice_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        if ($invoice->isPosted()) {
            return back()->with('error', __('messages.invoice_not_deletable'));
        }

        $oldValues = $invoice->toArray();
        $invoice->items()->delete();
        $invoice->delete();

        AuditLog::create([
            'action' => 'delete',
            'entity_type' => 'purchase_invoice',
            'entity_id' => $invoice->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
        ]);

        return redirect()->route('purchases.invoices.index')
            ->with('success', __('messages.invoice_deleted'));
    }

    public function post($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        if ($invoice->isPosted()) {
            return back()->with('error', __('messages.invoice_already_posted'));
        }

        try {
            DB::beginTransaction();

            $invoice->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Add logic to update vendor balance and stock levels
            $vendor = $invoice->vendor;
            $vendor->updateBalance($invoice->total_amount);

            // TODO: Add stock movement logic here

            AuditLog::create([
                'action' => 'post',
                'entity_type' => 'purchase_invoice',
                'entity_id' => $invoice->id,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('purchases.invoices.show', $invoice->id)
                ->with('success', __('messages.invoice_posted'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function unpost($id)
    {
        // Add unposting logic if allowed
        return back()->with('info', __('messages.feature_coming_soon'));
    }

    public function getWarehouses($branch_id)
    {
        $warehouses = Warehouse::where('branch_id', $branch_id)->active()->get();
        return response()->json($warehouses);
    }
}
