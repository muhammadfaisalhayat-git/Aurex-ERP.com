<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSignature;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function index()
    {
        $signatures = UserSignature::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('acp.system.signatures.index', compact('signatures'));
    }

    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('acp.system.signatures.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'signature_data' => 'required|string',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            UserSignature::where('user_id', $validated['user_id'])->update(['is_default' => false]);
        }

        $signature = UserSignature::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'signature_data' => $validated['signature_data'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        AuditLog::log('create', 'signature', $signature->id, null, ['title' => $signature->title]);

        return redirect()->route('acp.user-mgmt.signatures.index')
            ->with('success', __('messages.sm_signature_created'));
    }

    public function edit(UserSignature $signature)
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('acp.system.signatures.edit', compact('signature', 'users'));
    }

    public function update(Request $request, UserSignature $signature)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'signature_data' => 'required|string',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            UserSignature::where('user_id', $validated['user_id'])
                ->where('id', '!=', $signature->id)
                ->update(['is_default' => false]);
        }

        $old = $signature->toArray();
        $signature->update([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'signature_data' => $validated['signature_data'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        AuditLog::log('update', 'signature', $signature->id, $old, $signature->toArray());

        return redirect()->route('acp.user-mgmt.signatures.index')
            ->with('success', __('messages.sm_signature_updated'));
    }

    public function destroy(UserSignature $signature)
    {
        $old = $signature->toArray();
        $signature->delete();
        AuditLog::log('delete', 'signature', $signature->id, $old);

        return redirect()->route('acp.user-mgmt.signatures.index')
            ->with('success', __('messages.sm_signature_deleted'));
    }
}
