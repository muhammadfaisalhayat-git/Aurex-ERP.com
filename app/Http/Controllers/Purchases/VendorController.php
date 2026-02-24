<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view vendors')->only(['index', 'show', 'statement', 'ajaxSearch']);
        $this->middleware('can:create vendors')->only(['create', 'store']);
        $this->middleware('can:edit vendors')->only(['edit', 'update']);
        $this->middleware('can:delete vendors')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $vendors = $query->latest()->paginate(10);

        return view('purchases.vendors.index', compact('vendors'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        $nextCode = Vendor::generateNextCode('vendor');
        return view('purchases.vendors.create', compact('branches', 'nextCode'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('code')) {
            $request->merge(['code' => Vendor::generateNextCode($request->type ?? 'vendor')]);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vendors,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required_if:type,vendor|nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'required_if:type,vendor|nullable|string|max:50',
            'commercial_registration' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'type' => 'required|in:vendor,local_supplier',
        ]);

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['current_balance'] = $validated['opening_balance'];

        $vendor = Vendor::create($validated);

        AuditLog::log('create', 'vendor', $vendor->id, null, $vendor->toArray());

        return redirect()->route('purchases.vendors.index')
            ->with('success', __('messages.vendor_created'));
    }

    public function show(Vendor $vendor)
    {
        return view('purchases.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        $branches = Branch::active()->get();
        return view('purchases.vendors.edit', compact('vendor', 'branches'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vendors,code,' . $vendor->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required_if:type,vendor|nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'required_if:type,vendor|nullable|string|max:50',
            'commercial_registration' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'type' => 'required|in:vendor,local_supplier',
        ]);

        $oldValues = $vendor->toArray();
        $vendor->update($validated);

        AuditLog::log('update', 'vendor', $vendor->id, $oldValues, $vendor->toArray());

        return redirect()->route('purchases.vendors.index')
            ->with('success', __('messages.vendor_updated'));
    }

    public function destroy(Vendor $vendor)
    {
        // Check for transactions (purchase invoices, stock supplies)
        if ($vendor->purchaseInvoices()->exists() || $vendor->stockSupplies()->exists()) {
            return back()->with('error', __('messages.cannot_delete_vendor_with_transactions'));
        }

        $oldValues = $vendor->toArray();
        $vendor->delete();

        AuditLog::log('delete', 'vendor', $vendor->id, $oldValues);

        return redirect()->route('purchases.vendors.index')
            ->with('success', __('messages.vendor_deleted'));
    }

    public function statement($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('purchases.vendors.statement', compact('vendor'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $vendors = Vendor::active()
            ->where(function ($query) use ($search) {
                $query->where('name_en', 'like', "%$search%")
                    ->orWhere('name_ar', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            })
            ->limit(10)
            ->get(['id', 'code', 'name_en', 'name_ar']);

        return response()->json($vendors->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name . ' (' . $vendor->code . ')',
            ];
        }));
    }

    public function getNextCode(Request $request)
    {
        $type = $request->get('type', 'vendor');
        return response()->json([
            'code' => Vendor::generateNextCode($type)
        ]);
    }
}
