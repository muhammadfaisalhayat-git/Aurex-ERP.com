<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('auth.failed')])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => __('auth.inactive')])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Initialize tenant context in session
            session([
                'active_company_id' => $user->company_id,
                'active_branch_id' => $user->branch_id,
            ]);

            // Update last login info
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Log audit
            AuditLog::create([
                'action' => 'login',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => __('auth.failed')])->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'action' => 'logout',
                'entity_type' => 'user',
                'entity_id' => Auth::id(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
