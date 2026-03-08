<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportClaimController extends Controller
{
    public function index()
    {
        return view('transport.claims.index');
    }

    public function create()
    {
        return view('transport.claims.create');
    }

    public function show($id)
    {
        $claim = \App\Models\TransportClaim::with('transportOrder')->findOrFail($id);
        return view('transport.claims.show', compact('claim'));
    }

    public function review(\App\Models\TransportClaim $claim)
    {
        $claim->update(['status' => 'under_review', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        return back()->with('success', __('messages.claim_under_review'));
    }

    public function approve(\App\Models\TransportClaim $claim)
    {
        $claim->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        return back()->with('success', __('messages.claim_approved'));
    }

    public function reject(Request $request, \App\Models\TransportClaim $claim)
    {
        $claim->update([
            'status' => 'rejected',
            'resolution_notes' => $request->notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);
        return back()->with('success', __('messages.claim_rejected'));
    }

    public function settle(Request $request, \App\Models\TransportClaim $claim)
    {
        $request->validate([
            'settled_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($claim->settle($request->settled_amount, $request->notes)) {
            return back()->with('success', __('messages.claim_settled_successfully'));
        }

        return back()->with('error', __('messages.claim_already_settled'));
    }
}
