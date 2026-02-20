<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WorkCenterController extends Controller
{
    public function index()
    {
        $workCenters = WorkCenter::latest()->paginate(10);
        return view('production.work_centers.index', compact('workCenters'));
    }

    public function create()
    {
        return view('production.work_centers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:work_centers,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = Session::get('active_company_id');
        $validated['branch_id'] = Session::get('active_branch_id');

        WorkCenter::create($validated);

        return redirect()->route('production.work-centers.index')
            ->with('success', 'Work Center created successfully.');
    }

    public function edit(WorkCenter $workCenter)
    {
        return view('production.work_centers.edit', compact('workCenter'));
    }

    public function update(Request $request, WorkCenter $workCenter)
    {
        $validated = $request->validate([
            'code' => 'required|unique:work_centers,code,' . $workCenter->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $workCenter->update($validated);

        return redirect()->route('production.work-centers.index')
            ->with('success', 'Work Center updated successfully.');
    }

    public function destroy(WorkCenter $workCenter)
    {
        $workCenter->delete();
        return redirect()->route('production.work-centers.index')
            ->with('success', 'Work Center deleted successfully.');
    }
}
