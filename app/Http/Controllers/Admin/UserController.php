<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['branch', 'roles'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('branches', 'warehouses', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'employee_code' => 'nullable|string|max:50|unique:users',
            'branch_id' => 'nullable|exists:branches,id',
            'default_language' => 'required|in:en,ar',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'warehouses' => 'nullable|array',
            'warehouses.*' => 'exists:warehouses,id',
            'is_active' => 'boolean',
        ]);

        $companyId = null;
        if (!empty($validated['branch_id'])) {
            $companyId = Branch::find($validated['branch_id'])?->company_id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'employee_code' => $validated['employee_code'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'company_id' => $companyId,
            'default_language' => $validated['default_language'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Assign roles
        $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name');
        $user->syncRoles($roleNames);

        // Assign warehouses
        if (!empty($validated['warehouses'])) {
            $user->warehouses()->sync($validated['warehouses']);
        }

        // Log audit
        AuditLog::log('create', 'user', $user->id, null, $user->toArray());

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_created_success'));
    }

    public function show(User $user)
    {
        $user->load(['branch', 'roles', 'warehouses']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $branches = Branch::active()->get();
        $warehouses = Warehouse::active()->get();
        $roles = Role::orderBy('name')->get();
        $user->load(['roles', 'warehouses']);

        return view('admin.users.edit', compact('user', 'branches', 'warehouses', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'employee_code' => 'nullable|string|max:50|unique:users,employee_code,' . $user->id,
            'branch_id' => 'nullable|exists:branches,id',
            'default_language' => 'required|in:en,ar',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'warehouses' => 'nullable|array',
            'warehouses.*' => 'exists:warehouses,id',
            'is_active' => 'boolean',
        ]);

        $oldValues = $user->toArray();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'employee_code' => $validated['employee_code'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'default_language' => $validated['default_language'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if ($request->filled('branch_id')) {
            $updateData['company_id'] = Branch::find($validated['branch_id'])?->company_id;
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->has('password_reset_key')) {
            $updateData['password_reset_key'] = $request->password_reset_key;
        }

        $user->update($updateData);

        // Sync roles
        $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name');
        $user->syncRoles($roleNames);

        // Sync warehouses
        if (!empty($validated['warehouses'])) {
            $user->warehouses()->sync($validated['warehouses']);
        } else {
            $user->warehouses()->detach();
        }

        // Log audit
        AuditLog::log('update', 'user', $user->id, $oldValues, $user->toArray());

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        if ($user->hasRole('Super Admin')) {
            return back()->with('error', __('messages.cannot_delete_super_admin'));
        }

        $oldValues = $user->toArray();

        $user->delete();

        // Log audit
        AuditLog::log('delete', 'user', $user->id, $oldValues);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_deleted'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('messages.cannot_deactivate_self'));
        }

        $user->update(['is_active' => !$user->is_active]);

        // Log audit
        AuditLog::log($user->is_active ? 'activate' : 'deactivate', 'user', $user->id);

        return back()->with('success', $user->is_active
            ? __('messages.user_activated')
            : __('messages.user_deactivated'));
    }

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(12);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Log audit
        AuditLog::log('reset_password', 'user', $user->id);

        return back()->with('success', __('messages.password_reset', ['password' => $newPassword]));
    }

    public function editPermissions(User $user)
    {
        $permissions = Permission::all()->groupBy('module');
        $userPermissions = $user->permissions->pluck('id')->toArray();

        return view('admin.users.permissions', compact('user', 'permissions', 'userPermissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name');

        $user->syncPermissions($permissionNames);

        // Log audit
        AuditLog::log('update_permissions', 'user', $user->id, null, ['permissions' => $permissionNames->toArray()]);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.permissions_updated'));
    }
}
