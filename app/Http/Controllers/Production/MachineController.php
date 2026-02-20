<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\Machine;
use App\Models\Production\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::with('workCenter')->latest()->paginate(10);
        return view('production.machines.index', compact('machines'));
    }

    public function create()
    {
        $workCenters = WorkCenter::where('is_active', true)->get();
        return view('production.machines.create', compact('workCenters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_center_id' => 'required|exists:work_centers,id',
            'code' => 'required|unique:machines,code',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'hourly_cost' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance,busy,offline',
        ]);

        $validated['company_id'] = Session::get('active_company_id');

        Machine::create($validated);

        return redirect()->route('production.machines.index')
            ->with('success', 'Machine created successfully.');
    }

    public function edit(Machine $machine)
    {
        $workCenters = WorkCenter::where('is_active', true)->get();
        return view('production.machines.edit', compact('machine', $workCenters));
    }

    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'work_center_id' => 'required|exists:work_centers,id',
            'code' => 'required|unique:machines,code,' . $machine->id,
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'hourly_cost' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance,busy,offline',
        ]);

        $machine->update($validated);

        return redirect()->route('production.machines.index')
            ->with('success', 'Machine updated successfully.');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return redirect()->route('production.machines.index')
            ->with('success', 'Machine deleted successfully.');
    }
}
