<?php

namespace App\Http\Controllers\Healthcare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = \App\Models\Healthcare\Patient::all();
        return view('healthcare.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('healthcare.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = session('active_company_id');
        $validated['branch_id'] = session('active_branch_id');
        $validated['code'] = 'PAT-' . str_pad(\App\Models\Healthcare\Patient::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['is_active'] = $request->has('is_active');

        \App\Models\Healthcare\Patient::create($validated);

        return redirect()->route('healthcare.patients.index')->with('success', __('messages.patient_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = \App\Models\Healthcare\Patient::with('appointments.doctor', 'appointments.service')->findOrFail($id);
        return view('healthcare.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $patient = \App\Models\Healthcare\Patient::findOrFail($id);
        return view('healthcare.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $patient = \App\Models\Healthcare\Patient::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $patient->update($validated);

        return redirect()->route('healthcare.patients.index')->with('success', __('messages.patient_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = \App\Models\Healthcare\Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('healthcare.patients.index')->with('success', __('messages.patient_deleted_successfully'));
    }

    /**
     * Print the specified resource.
     */
    public function print(string $id)
    {
        $patient = \App\Models\Healthcare\Patient::with('appointments.doctor', 'appointments.service')->findOrFail($id);
        return view('healthcare.patients.print', compact('patient'));
    }
}
