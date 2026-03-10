<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\TaxSetting;
use App\Models\DocumentNumber;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ArabicShaper;
use Illuminate\Support\Facades\Log;
use App\Models\SalesInvoice;
use App\Models\BankAccount;

class SalesReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view returns')->only(['index', 'show']);
        $this->middleware('can:create returns')->only(['create', 'store']);
        $this->middleware('can:edit returns')->only(['edit', 'update']);
        $this->middleware('can:delete returns')->only(['destroy']);
        $this->middleware('can:post returns')->only(['post']);
    }

    public function index(Request $request)
    {
        $query = SalesReturn::with(['customer', 'branch', 'warehouse']);

        if ($request->filled('return_number')) {
            $query->where('return_number', 'like', "%{$request->return_number}%")
                ->orWhere('document_number', 'like', "%{$request->return_number}%");
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(20);
        $customers = Customer::active()->get();

        return view('sales.returns.index', compact('returns', 'customers'));
    }

    public function create()
    {
        $customers = Customer::active()->get();
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $products = Product::sellable()->active()->get();
        $taxSetting = TaxSetting::first();
        $documentNumber = DocumentNumber::generate('sales_return');
        $invoices = SalesInvoice::with('customer')->posted()->get();
        $bankAccounts = BankAccount::active()->get();

        return view('sales.returns.create', compact('customers', 'branches', 'warehouses', 'products', 'taxSetting', 'documentNumber', 'invoices', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|unique:sales_returns',
            'return_number' => 'required|unique:sales_returns',
            'return_date' => 'required|date',
            'customer_id' => 'required_if:return_type,credit|nullable|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'return_type' => 'required|in:cash,credit',
            'bank_account_id' => 'required_if:return_type,cash|nullable|exists:bank_accounts,id',
            'return_reason' => 'required|string',
            'reason_description' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.measurement_unit_id' => 'required|exists:measurement_units,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $taxSetting = TaxSetting::first();
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];

            // Quantity Validation
            if ($validated['sales_invoice_id']) {
                $invoiceItem = \App\Models\SalesInvoiceItem::where('sales_invoice_id', $validated['sales_invoice_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$invoiceItem) {
                    return back()->withInput()->with('error', 'Product ' . (\App\Models\Product::find($item['product_id'])->name ?? '') . ' not found in original invoice.');
                }

                if ($item['quantity'] > $invoiceItem->quantity) {
                    return back()->withInput()->with('error', 'Return quantity for ' . ($invoiceItem->product->name ?? '') . ' exceeds original invoice quantity.');
                }
            }

            $lineTax = $lineTotal * ($taxSetting?->default_tax_rate ?? 0) / 100;
            $subtotal += $lineTotal;
            $taxAmount += $lineTax;
        }

        $salesReturn = SalesReturn::create([
            'company_id' => session('active_company_id'),
            'document_number' => $validated['document_number'],
            'return_number' => $validated['return_number'],
            'return_date' => $validated['return_date'],
            'customer_id' => $validated['customer_id'],
            'branch_id' => $validated['branch_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'sales_invoice_id' => $validated['sales_invoice_id'],
            'return_type' => $validated['return_type'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'return_reason' => $validated['return_reason'],
            'reason_description' => $validated['reason_description'],
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount,
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $lineTax = $lineTotal * ($taxSetting?->default_tax_rate ?? 0) / 100;

            $salesReturn->items()->create([
                'product_id' => $item['product_id'],
                'measurement_unit_id' => $item['measurement_unit_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_amount' => $lineTax,
                'total_amount' => $lineTotal + $lineTax,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        AuditLog::log('create', 'sales_return', $salesReturn->id, null, $salesReturn->toArray());

        return redirect()->route('sales.returns.show', $salesReturn)
            ->with('success', __('messages.return_created'));
    }

    public function post(SalesReturn $salesReturn)
    {
        if (!auth()->user()->hasPermissionTo('post returns')) {
            return back()->with('error', __('messages.no_permission_to_post') ?? 'No permission to post.');
        }

        if ($salesReturn->post()) {
            AuditLog::log('post', 'sales_return', $salesReturn->id);

            return back()->with('success', __('messages.return_posted') ?? 'Return posted successfully.');
        }

        return back()->with('error', __('messages.return_post_failed') ?? 'Return posting failed.');
    }

    public function unpost(SalesReturn $salesReturn)
    {
        if (!auth()->user()->can('unpost returns')) {
            return back()->with('error', __('messages.no_permission_to_unpost') ?? 'No permission to unpost.');
        }

        if ($salesReturn->unpost()) {
            AuditLog::log('unpost', 'sales_return', $salesReturn->id);

            return back()->with('success', __('messages.return_unposted') ?? 'Return unposted successfully.');
        }

        return back()->with('error', __('messages.return_unpost_failed') ?? 'Return unposting failed.');
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'branch', 'warehouse', 'items.product', 'items.measurementUnit', 'salesInvoice', 'creator']);
        return view('sales.returns.show', compact('salesReturn'));
    }

    public function print(SalesReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'branch', 'warehouse', 'items.product', 'items.measurementUnit', 'salesInvoice', 'creator', 'company']);
        return view('sales.returns.print', compact('salesReturn'));
    }

    public function pdf(SalesReturn $salesReturn)
    {
        try {
            $salesReturn->load(['customer', 'branch', 'warehouse', 'items.product', 'items.measurementUnit', 'salesInvoice', 'creator', 'company']);

            // Base64 logo for PDF
            $logoBase64 = null;
            if ($salesReturn->company?->logo) {
                $path = public_path('storage/' . $salesReturn->company->logo);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = @file_get_contents($path);
                    if ($data) {
                        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }
            }

            $arabicShaper = app(ArabicShaper::class);

            // Reshape Arabic text for PDF
            if ($salesReturn->company) {
                $salesReturn->company_name_ar = $arabicShaper->shape($salesReturn->company->name_ar ?? $salesReturn->company->name_en);
            }
            $salesReturn->customer_name_ar = $arabicShaper->shape($salesReturn->customer?->name_ar ?? $salesReturn->customer?->name_en ?? '');
            $salesReturn->notes_ar = $arabicShaper->shape($salesReturn->notes ?? '');

            foreach ($salesReturn->items as $item) {
                $item->product_name_ar = $arabicShaper->shape($item->product?->name_ar ?? $item->product?->name_en);
            }

            $pdf = PDF::loadView('sales.returns.pdf', compact('salesReturn', 'logoBase64'));

            return $pdf->download("return_{$salesReturn->document_number}.pdf");
        } catch (\Exception $e) {
            Log::error('Sales Return PDF Generation Error: ' . $e->getMessage(), [
                'return_id' => $salesReturn->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function destroy(SalesReturn $salesReturn)
    {
        if ($salesReturn->isPosted()) {
            return back()->with('error', __('messages.return_not_deletable') ?? 'Posted returns cannot be deleted.');
        }

        $oldValues = $salesReturn->toArray();
        $salesReturn->items()->delete();
        $salesReturn->delete();

        AuditLog::log('delete', 'sales_return', $salesReturn->id, $oldValues);

        return redirect()->route('sales.returns.index')
            ->with('success', __('messages.return_deleted') ?? 'Return deleted successfully.');
    }
}
