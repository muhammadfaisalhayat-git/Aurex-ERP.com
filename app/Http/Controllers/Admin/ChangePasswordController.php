<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('acp.system.change-password.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('messages.sm_current_password_incorrect')]);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        AuditLog::log('change_password', 'user', $user->id);

        return back()->with('success', __('messages.sm_password_changed'));
    }
}
