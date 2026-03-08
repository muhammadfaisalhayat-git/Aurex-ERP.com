<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        // Seed dummy data if table is empty
        if (UserGroup::count() === 0) {
            $this->seedDummyData();
        }

        $groups = UserGroup::withCount('users')->orderBy('name')->paginate(20);
        return view('acp.system.user-groups.index', compact('groups'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('acp.system.user-groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:user_groups',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $group = UserGroup::create([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['users'])) {
            $group->users()->sync($validated['users']);
        }

        AuditLog::log('create', 'user_group', $group->id, null, $group->toArray());

        return redirect()->route('acp.system.user-groups.index')
            ->with('success', __('messages.sm_group_created'));
    }

    public function show(UserGroup $userGroup)
    {
        $userGroup->load('users');
        return view('acp.system.user-groups.show', compact('userGroup'));
    }

    public function edit(UserGroup $userGroup)
    {
        $users = User::orderBy('name')->get();
        $userGroup->load('users');
        return view('acp.system.user-groups.edit', compact('userGroup', 'users'));
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:user_groups,name,' . $userGroup->id,
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $old = $userGroup->toArray();

        $userGroup->update([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $userGroup->users()->sync($validated['users'] ?? []);

        AuditLog::log('update', 'user_group', $userGroup->id, $old, $userGroup->toArray());

        return redirect()->route('acp.system.user-groups.index')
            ->with('success', __('messages.sm_group_updated'));
    }

    public function destroy(UserGroup $userGroup)
    {
        $old = $userGroup->toArray();
        $userGroup->users()->detach();
        $userGroup->delete();

        AuditLog::log('delete', 'user_group', $userGroup->id, $old);

        return redirect()->route('acp.system.user-groups.index')
            ->with('success', __('messages.sm_group_deleted'));
    }

    private function seedDummyData()
    {
        $groups = [
            ['name' => 'Administrators', 'name_ar' => 'المسؤولون', 'description' => 'Full system access administrators'],
            ['name' => 'Sales Team', 'name_ar' => 'فريق المبيعات', 'description' => 'Sales department users'],
            ['name' => 'Accountants', 'name_ar' => 'المحاسبون', 'description' => 'Accounting department users'],
            ['name' => 'Warehouse Staff', 'name_ar' => 'موظفو المستودع', 'description' => 'Warehouse and inventory staff'],
            ['name' => 'HR Department', 'name_ar' => 'قسم الموارد البشرية', 'description' => 'Human resources personnel'],
        ];

        foreach ($groups as $g) {
            $group = UserGroup::create(array_merge($g, ['is_active' => true]));
            $userIds = User::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $group->users()->sync($userIds);
        }
    }
}
