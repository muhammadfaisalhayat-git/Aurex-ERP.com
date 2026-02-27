<?php

namespace App\Http\Controllers\Healthcare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = \App\Models\Healthcare\Doctor::all();
        return view('healthcare.doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('healthcare.doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'specialization' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = session('active_company_id');
        $validated['code'] = 'DOC-' . str_pad(\App\Models\Healthcare\Doctor::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['is_active'] = $request->has('is_active');

        \App\Models\Healthcare\Doctor::create($validated);

        return redirect()->route('healthcare.doctors.index')->with('success', __('messages.doctor_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = \App\Models\Healthcare\Doctor::with('appointments.patient', 'appointments.service')->findOrFail($id);
        return view('healthcare.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $doctor = \App\Models\Healthcare\Doctor::findOrFail($id);
        return view('healthcare.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $doctor = \App\Models\Healthcare\Doctor::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'specialization' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $doctor->update($validated);

        return redirect()->route('healthcare.doctors.index')->with('success', __('messages.doctor_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = \App\Models\Healthcare\Doctor::findOrFail($id);
        $doctor->delete();

        return redirect()->route('healthcare.doctors.index')->with('success', __('messages.doctor_deleted_successfully'));
    }

    /**
     * Print the specified resource.
     */
    public function print(string $id)
    {
        $doctor = \App\Models\Healthcare\Doctor::with('appointments.patient', 'appointments.service')->findOrFail($id);
        return view('healthcare.doctors.print', compact('doctor'));
    }
}
