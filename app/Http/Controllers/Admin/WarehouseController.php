<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('branch')
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        return view('admin.warehouses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $warehouse = Warehouse::create($validated);

        AuditLog::create([
            'action' => 'create',
            'entity_type' => 'warehouse',
            'entity_id' => $warehouse->id,
            'user_id' => auth()->id(),
            'new_values' => $warehouse->toArray(),
        ]);

        return redirect()->route('admin.warehouses.index')
            ->with('success', __('messages.warehouse_created'));
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['branch', 'users']);
        return view('admin.warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $branches = Branch::active()->get();
        return view('admin.warehouses.edit', compact('warehouse', 'branches'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $oldValues = $warehouse->toArray();
        $warehouse->update($validated);

        AuditLog::create([
            'action' => 'update',
            'entity_type' => 'warehouse',
            'entity_id' => $warehouse->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
            'new_values' => $warehouse->toArray(),
        ]);

        return redirect()->route('admin.warehouses.index')
            ->with('success', __('messages.warehouse_updated'));
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->stockBalances()->sum('quantity') > 0) {
            return back()->with('error', __('messages.cannot_delete_warehouse_with_stock'));
        }

        $oldValues = $warehouse->toArray();
        $warehouse->delete();

        AuditLog::create([
            'action' => 'delete',
            'entity_type' => 'warehouse',
            'entity_id' => $warehouse->id,
            'user_id' => auth()->id(),
            'old_values' => $oldValues,
        ]);

        return redirect()->route('admin.warehouses.index')
            ->with('success', __('messages.warehouse_deleted'));
    }

    public function ajaxByBranch(Request $request)
    {
        $branch_id = $request->get('branch_id');
        $warehouses = Warehouse::where('branch_id', $branch_id)
            ->active()
            ->get(['id', 'name_en', 'name_ar']);

        return response()->json($warehouses);
    }
}
