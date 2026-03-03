<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportContractController extends Controller
{
    public function index()
    {
        $contracts = \App\Models\TransportContract::paginate(10);
        return view('transport.contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('transport.contracts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_number' => 'required|string|unique:transport_contracts,contract_number',
            'contract_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contractor_name' => 'required|string',
            'contractor_phone' => 'nullable|string',
            'contract_value' => 'required|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:active,pending,closed,cancelled',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();

        \App\Models\TransportContract::create($validated);

        return redirect()->route('transport.contracts.index')
            ->with('success', __('messages.contract_created_successfully'));
    }

    public function show(\App\Models\TransportContract $contract)
    {
        return view('transport.contracts.show', compact('contract'));
    }

    public function edit(\App\Models\TransportContract $contract)
    {
        return view('transport.contracts.edit', compact('contract'));
    }

    public function update(Request $request, \App\Models\TransportContract $contract)
    {
        $validated = $request->validate([
            'contract_number' => 'required|string|unique:transport_contracts,contract_number,' . $contract->id,
            'contract_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contractor_name' => 'required|string',
            'contractor_phone' => 'nullable|string',
            'contract_value' => 'required|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:active,pending,closed,cancelled',
        ]);

        $contract->update($validated);

        return redirect()->route('transport.contracts.index')
            ->with('success', __('messages.contract_updated_successfully'));
    }

    public function destroy(\App\Models\TransportContract $contract)
    {
        $contract->delete();
        return redirect()->route('transport.contracts.index')
            ->with('success', __('messages.contract_deleted_successfully'));
    }

    public function close(\App\Models\TransportContract $contract)
    {
        $contract->update([
            'status' => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        return redirect()->route('transport.contracts.index')
            ->with('success', __('messages.contract_closed_successfully'));
    }
}
