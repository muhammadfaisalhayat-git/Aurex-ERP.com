<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\AuditLog;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->paginate(20);

        return view('acp.user-mgmt.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('acp.user-mgmt.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'display_name_en' => 'required|string|max:255',
            'display_name_ar' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name_en' => $validated['display_name_en'],
            'display_name_ar' => $validated['display_name_ar'],
            'guard_name' => 'web',
        ]);

        // Assign permissions
        if (!empty($validated['permissions'])) {
            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name');
            $role->syncPermissions($permissionNames);
        }

        // Log audit
        AuditLog::log('create', 'role', $role->id, null, $role->toArray());

        return redirect()->route('acp.user-mgmt.roles.index')
            ->with('success', __('messages.role_created'));
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        return view('acp.user-mgmt.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', __('messages.cannot_edit_system_role'));
        }

        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('acp.user-mgmt.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', __('messages.cannot_edit_system_role'));
        }

        $validated = $request->validate([
            'display_name_en' => 'required|string|max:255',
            'display_name_ar' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $oldValues = $role->toArray();

        $role->update([
            'display_name_en' => $validated['display_name_en'],
            'display_name_ar' => $validated['display_name_ar'],
        ]);

        // Sync permissions
        $permissionIds = $validated['permissions'] ?? [];
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name');
        $role->syncPermissions($permissionNames);

        // Log audit
        AuditLog::log('update', 'role', $role->id, $oldValues, $role->toArray());

        return redirect()->route('acp.user-mgmt.roles.index')
            ->with('success', __('messages.role_updated'));
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', __('messages.cannot_delete_system_role'));
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', __('messages.cannot_delete_role_with_users'));
        }

        $oldValues = $role->toArray();

        $role->delete();

        // Log audit
        AuditLog::log('delete', 'role', $role->id, $oldValues);

        return redirect()->route('acp.user-mgmt.roles.index')
            ->with('success', __('messages.role_deleted'));
    }

    public function editPermissions(Role $role)
    {
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('acp.user-mgmt.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name');

        $role->syncPermissions($permissionNames);

        // Log audit
        AuditLog::log('update_permissions', 'role', $role->id, null, ['permissions' => $permissionNames->toArray()]);

        return redirect()->route('acp.user-mgmt.roles.show', $role)
            ->with('success', __('messages.permissions_updated'));
    }
}
