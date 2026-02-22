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
        $invoices = SalesInvoice::posted()->get();
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

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'branch', 'warehouse', 'items.product', 'salesInvoice', 'creator']);
        return view('sales.returns.show', compact('salesReturn'));
    }
}
