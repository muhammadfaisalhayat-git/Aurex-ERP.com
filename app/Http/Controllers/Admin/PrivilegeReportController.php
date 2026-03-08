<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PrivilegeReportController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $users = User::with('roles', 'permissions')->orderBy('name')->get();

        $selectedRole = $request->get('role');
        $filteredUsers = $users;

        if ($selectedRole) {
            $filteredUsers = $users->filter(fn($u) => $u->roles->contains('name', $selectedRole));
        }

        // Build privilege matrix
        $permissionGroups = Permission::all()->groupBy(function ($p) {
            $parts = explode(' ', $p->name);
            return $parts[1] ?? 'general';
        });

        return view('acp.system.privilege-reports.index', compact('roles', 'filteredUsers', 'permissionGroups', 'selectedRole'));
    }
}
