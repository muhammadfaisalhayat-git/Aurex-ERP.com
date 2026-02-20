<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\FuelLog;
use App\Models\Logistics\DeliveryVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FuelLogController extends Controller
{
    public function index()
    {
        $logs = FuelLog::with('vehicle')->latest()->paginate(10);
        return view('logistics.fuel_logs.index', compact('logs'));
    }

    public function create()
    {
        $vehicles = DeliveryVehicle::where('status', '!=', 'retired')->get();
        return view('logistics.fuel_logs.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_vehicle_id' => 'required|exists:delivery_vehicles,id',
            'entry_date' => 'required|date',
            'liters' => 'required|numeric|min:0.01',
            'cost_per_liter' => 'required|numeric|min:0.01',
            'odometer_reading' => 'required|integer',
            'fuel_station' => 'nullable|string',
        ]);

        $validated['total_cost'] = $validated['liters'] * $validated['cost_per_liter'];
        $validated['company_id'] = Session::get('active_company_id');
        $validated['logged_by'] = auth()->id();

        $log = FuelLog::create($validated);
        $log->post(); // Trigger accounting integration immediately for fuel logs

        return redirect()->route('logistics.fuel-logs.index')
            ->with('success', 'Fuel Log recorded and posted to ledger.');
    }
}
