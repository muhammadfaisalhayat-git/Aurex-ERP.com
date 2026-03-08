<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserHeaderSetting;
use Illuminate\Http\Request;

class UserHeaderSettingController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::with('company', 'branch')->orderBy('name')->get();
        $settings = UserHeaderSetting::with('user')->get()->keyBy('user_id');

        return view('acp.system.user-header.index', compact('users', 'settings'));
    }

    public function edit($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $setting = UserHeaderSetting::firstOrCreate(
            ['user_id' => $userId],
            [
                'header_title' => $user->name,
                'show_company' => true,
                'show_branch' => true,
                'show_date' => true,
            ]
        );

        return view('acp.system.user-header.edit', compact('user', 'setting'));
    }

    public function update(Request $request, $userId)
    {
        $validated = $request->validate([
            'header_title' => 'nullable|string|max:255',
            'show_company' => 'boolean',
            'show_branch' => 'boolean',
            'show_date' => 'boolean',
        ]);

        UserHeaderSetting::updateOrCreate(
            ['user_id' => $userId],
            [
                'header_title' => $validated['header_title'] ?? null,
                'show_company' => $validated['show_company'] ?? false,
                'show_branch' => $validated['show_branch'] ?? false,
                'show_date' => $validated['show_date'] ?? false,
            ]
        );

        return redirect()->route('acp.system.user-header.index')
            ->with('success', __('messages.sm_header_updated'));
    }
}
