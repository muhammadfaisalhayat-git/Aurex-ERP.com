<?php

namespace App\Http\Controllers\Healthcare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedicalServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = \App\Models\Healthcare\MedicalService::all();
        return view('healthcare.medical_services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $revenueAccounts = \App\Models\ChartOfAccount::where('type', 'revenue')->get();
        return view('healthcare.medical_services.create', compact('revenueAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'revenue_account_id' => 'required|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = session('active_company_id');
        $validated['code'] = 'SRV-' . str_pad(\App\Models\Healthcare\MedicalService::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['is_active'] = $request->has('is_active');

        \App\Models\Healthcare\MedicalService::create($validated);

        return redirect()->route('healthcare.medical-services.index')->with('success', __('messages.service_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = \App\Models\Healthcare\MedicalService::with('appointments.patient', 'appointments.doctor')->findOrFail($id);
        return view('healthcare.medical_services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = \App\Models\Healthcare\MedicalService::findOrFail($id);
        $revenueAccounts = \App\Models\ChartOfAccount::where('type', 'revenue')->get();
        return view('healthcare.medical_services.edit', compact('service', 'revenueAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = \App\Models\Healthcare\MedicalService::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'revenue_account_id' => 'required|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('healthcare.medical-services.index')->with('success', __('messages.service_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = \App\Models\Healthcare\MedicalService::findOrFail($id);
        $service->delete();

        return redirect()->route('healthcare.medical-services.index')->with('success', __('messages.service_deleted_successfully'));
    }
}
