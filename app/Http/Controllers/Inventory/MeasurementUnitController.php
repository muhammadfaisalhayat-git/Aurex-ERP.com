<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\MeasurementUnit;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = MeasurementUnit::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('name')->paginate(15);
        return view('inventory.measurement-units.index', compact('units'));
    }

    public function create()
    {
        return view('inventory.measurement-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        MeasurementUnit::create($validated);

        return redirect()->route('inventory.measurement.units.index')
            ->with('success', __('messages.record_created'));
    }

    public function edit(MeasurementUnit $measurementUnit)
    {
        return view('inventory.measurement-units.edit', compact('measurementUnit'));
    }

    public function update(Request $request, MeasurementUnit $measurementUnit)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $measurementUnit->update($validated);

        return redirect()->route('inventory.measurement.units.index')
            ->with('success', __('messages.record_updated'));
    }

    public function destroy(MeasurementUnit $measurementUnit)
    {
        // Simple check if it's used before deleting (optional/advanced)
        if ($measurementUnit->productUnits()->count() > 0) {
            return back()->with('error', 'Cannot delete measurement unit because it is used in products.');
        }

        $measurementUnit->delete();

        return redirect()->route('inventory.measurement.units.index')
            ->with('success', __('messages.record_deleted'));
    }
}
