<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DeliveryVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DeliveryVehicleController extends Controller
{
    public function index()
    {
        $vehicles = DeliveryVehicle::latest()->paginate(10);
        return view('logistics.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('logistics.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:delivery_vehicles,plate_number',
            'brand' => 'required|string',
            'model' => 'required|string',
            'type' => 'required|string',
            'fuel_type' => 'required|string',
            'max_payload' => 'nullable|numeric|min:0',
        ]);

        $validated['company_id'] = Session::get('active_company_id');

        DeliveryVehicle::create($validated);

        return redirect()->route('logistics.vehicles.index')
            ->with('success', 'Delivery Vehicle registered successfully.');
    }

    public function edit(DeliveryVehicle $vehicle)
    {
        return view('logistics.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, DeliveryVehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:delivery_vehicles,plate_number,' . $vehicle->id,
            'brand' => 'required|string',
            'model' => 'required|string',
            'type' => 'required|string',
            'fuel_type' => 'required|string',
            'max_payload' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,in_transit,maintenance,retired',
        ]);

        $vehicle->update($validated);

        return redirect()->route('logistics.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }
}
