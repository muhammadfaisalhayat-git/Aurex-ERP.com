<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PrivilegeController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::all()->groupBy(function ($p) {
            $parts = explode(' ', $p->name);
            return $parts[1] ?? 'general';
        });

        return view('acp.system.privileges.index', compact('roles', 'permissions'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy(function ($p) {
            $parts = explode(' ', $p->name);
            return $parts[1] ?? 'general';
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('acp.system.privileges.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name');
        $role->syncPermissions($permissionNames);

        \App\Models\AuditLog::log('update_privileges', 'role', $role->id, null, ['permissions' => $permissionNames->toArray()]);

        return redirect()->route('acp.system.privileges.index')
            ->with('success', __('messages.sm_privileges_updated'));
    }
}
