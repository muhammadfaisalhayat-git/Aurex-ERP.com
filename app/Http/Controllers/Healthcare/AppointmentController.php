<?php

namespace App\Http\Controllers\Healthcare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = \App\Models\Healthcare\Appointment::with(['patient', 'doctor', 'service'])->get();
        return view('healthcare.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = \App\Models\Healthcare\Patient::where('is_active', true)->get();
        $doctors = \App\Models\Healthcare\Doctor::where('is_active', true)->get();
        $services = \App\Models\Healthcare\MedicalService::where('is_active', true)->get();
        return view('healthcare.appointments.create', compact('patients', $doctors, 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:healthcare_patients,id',
            'doctor_id' => 'required|exists:healthcare_doctors,id',
            'service_id' => 'required|exists:healthcare_medical_services,id',
            'appointment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $service = \App\Models\Healthcare\MedicalService::findOrFail($request->service_id);

        $validated['company_id'] = session('active_company_id');
        $validated['branch_id'] = session('active_branch_id');
        $validated['status'] = 'scheduled';
        $validated['billing_status'] = 'unbilled';
        $validated['total_amount'] = $service->cost;

        \App\Models\Healthcare\Appointment::create($validated);

        return redirect()->route('healthcare.appointments.index')->with('success', __('messages.appointment_scheduled_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = \App\Models\Healthcare\Appointment::with(['patient', 'doctor', 'service'])->findOrFail($id);
        return view('healthcare.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $appointment = \App\Models\Healthcare\Appointment::findOrFail($id);
        $patients = \App\Models\Healthcare\Patient::where('is_active', true)->get();
        $doctors = \App\Models\Healthcare\Doctor::where('is_active', true)->get();
        $services = \App\Models\Healthcare\MedicalService::where('is_active', true)->get();
        return view('healthcare.appointments.edit', compact('appointment', 'patients', 'doctors', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $appointment = \App\Models\Healthcare\Appointment::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:healthcare_patients,id',
            'doctor_id' => 'required|exists:healthcare_doctors,id',
            'service_id' => 'required|exists:healthcare_medical_services,id',
            'appointment_date' => 'required|date',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $service = \App\Models\Healthcare\MedicalService::findOrFail($request->service_id);
        $validated['total_amount'] = $service->cost;

        $appointment->update($validated);

        return redirect()->route('healthcare.appointments.index')->with('success', __('messages.appointment_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = \App\Models\Healthcare\Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('healthcare.appointments.index')->with('success', __('messages.appointment_deleted_successfully'));
    }

    /**
     * Print the specified resource.
     */
    public function print(string $id)
    {
        $appointment = \App\Models\Healthcare\Appointment::with(['patient', 'doctor', 'service'])->findOrFail($id);
        return view('healthcare.appointments.print', compact('appointment'));
    }
    /**
     * Generate invoice/bill for the appointment.
     */
    public function invoice(string $id)
    {
        $appointment = \App\Models\Healthcare\Appointment::findOrFail($id);

        if ($appointment->billing_status === 'invoiced') {
            return redirect()->back()->with('error', __('messages.appointment_already_invoiced'));
        }

        try {
            $accountingService = app(\App\Services\AccountingService::class);
            $accountingService->postHealthcareAppointment($appointment);

            $appointment->update([
                'billing_status' => 'invoiced',
            ]);

            return redirect()->route('healthcare.appointments.index')->with('success', __('messages.appointment_invoiced_successfully'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.error_invoicing_appointment') . ': ' . $e->getMessage());
        }
    }
}
