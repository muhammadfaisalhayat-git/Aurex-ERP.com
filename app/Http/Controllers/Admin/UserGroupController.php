<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        $groups = UserGroup::withCount('users')->orderBy('name')->paginate(20);
        return view('acp.user-mgmt.user-groups.index', compact('groups'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('acp.user-mgmt.user-groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $group = UserGroup::create($validated);

        if ($request->has('users')) {
            $group->users()->sync($request->users);
        }

        AuditLog::log('create', 'user_group', $group->id, null, $group->toArray());

        return redirect()->route('acp.user-mgmt.user-groups.index')
            ->with('success', 'User Group created successfully.');
    }

    public function show(UserGroup $userGroup)
    {
        return view('acp.user-mgmt.user-groups.show', compact('userGroup'));
    }

    public function edit(UserGroup $userGroup)
    {
        $users = User::orderBy('name')->get();
        return view('acp.user-mgmt.user-groups.edit', compact('userGroup', 'users'));
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $old = $userGroup->toArray();
        $userGroup->update($validated);

        if ($request->has('users')) {
            $userGroup->users()->sync($request->users);
        } else {
            $userGroup->users()->sync([]);
        }

        AuditLog::log('update', 'user_group', $userGroup->id, $old, $userGroup->toArray());

        return redirect()->route('acp.user-mgmt.user-groups.index')
            ->with('success', 'User Group updated successfully.');
    }

    public function destroy(UserGroup $userGroup)
    {
        $old = $userGroup->toArray();
        $userGroup->delete();
        AuditLog::log('delete', 'user_group', $userGroup->id, $old);

        return redirect()->route('acp.user-mgmt.user-groups.index')
            ->with('success', 'User Group deleted successfully.');
    }
}
