<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Branch;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view customers')->only(['index', 'show', 'statement', 'ajaxSearch']);
        $this->middleware('can:create customers')->only(['create', 'store']);
        $this->middleware('can:edit customers')->only(['edit', 'update']);
        $this->middleware('can:delete customers')->only(['destroy']);
    }
    public function index(Request $request)
    {
        $query = Customer::with(['group', 'branch', 'salesman']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sales.customers.index', compact('customers'));
    }

    public function create()
    {
        $customerGroups = CustomerGroup::where('is_active', true)->get();
        $branches = Branch::active()->get();
        // Assuming salesmen have a specific role or just getting all users for now
        $salesmen = User::where('is_active', true)->get();
        $nextCode = Customer::generateNextCode();

        return view('sales.customers.create', compact('customerGroups', 'branches', 'salesmen', 'nextCode'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('code')) {
            $request->merge(['code' => Customer::generateNextCode()]);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:customers,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'group_id' => 'nullable|exists:customer_groups,id',
            'branch_id' => 'nullable|exists:branches,id',
            'salesman_id' => 'nullable|exists:users,id',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'commercial_registration' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0',
            'opening_balance' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['credit_days'] = $validated['credit_days'] ?? 0;
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        $customer = Customer::create($validated);

        AuditLog::create([
            'action' => 'create',
            'entity_type' => 'customer',
            'entity_id' => $customer->id,
            'user_id' => auth()->id(),
            'new_values' => $customer->toArray(),
        ]);

        return redirect()->route('sales.customers.index')
            ->with('success', __('messages.customer_created'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['group', 'branch', 'salesman', 'customerRequests', 'quotations', 'salesInvoices']);
        return view('sales.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customerGroups = CustomerGroup::where('is_active', true)->get();
        $branches = Branch::active()->get();
        $salesmen = User::where('is_active', true)->get();

        return view('sales.customers.edit', compact('customer', 'customerGroups', 'branches', 'salesmen'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:customers,code,' . $customer->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'group_id' => 'nullable|exists:customer_groups,id',
            'branch_id' => 'nullable|exists:branches,id',
            'salesman_id' => 'nullable|exists:users,id',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'commercial_registration' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0',
            'opening_balance' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['credit_days'] = $validated['credit_days'] ?? 0;
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        $oldValues = $customer->toArray();
        $customer->update($validated);

        AuditLog::create([
            'action' => 'update',
            'entity_type' => 'customer',
            'entity_id' => $customer->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
            'new_values' => $customer->toArray(),
        ]);

        return redirect()->route('sales.customers.index')
            ->with('success', __('messages.customer_updated'));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->quotations()->exists() || $customer->salesInvoices()->exists()) {
            return back()->with('error', __('messages.cannot_delete_customer_with_transactions'));
        }

        $oldValues = $customer->toArray();
        $customer->delete();

        AuditLog::create([
            'action' => 'delete',
            'entity_type' => 'customer',
            'entity_id' => $customer->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
        ]);

        return redirect()->route('sales.customers.index')
            ->with('success', __('messages.customer_deleted'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $customers = Customer::active()
            ->where(function ($query) use ($search) {
                $query->where('name_en', 'like', "%$search%")
                    ->orWhere('name_ar', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            })
            ->limit(10)
            ->get();

        return response()->json($customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'code' => $customer->code,
                'text' => $customer->name . ' (' . $customer->code . ')',
            ];
        }));
    }
}
