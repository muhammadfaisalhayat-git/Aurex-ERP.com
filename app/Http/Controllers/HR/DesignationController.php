<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Department;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view designations')->only(['index', 'show']);
        $this->middleware('permission:manage designations')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $designations = Designation::with('department')->paginate(20);
        return view('hr.designations.index', compact('designations'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('hr.designations.create', compact('departments'));
    }

    public function show(Designation $designation)
    {
        $designation->load(['department', 'employees']);
        return view('hr.designations.show', compact('designation'));
    }

    public function edit(Designation $designation)
    {
        $departments = Department::active()->get();
        return view('hr.designations.edit', compact('designation', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:designations,code',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Designation::create($validated);

        return redirect()->route('hr.designations.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:designations,code,' . $designation->id,
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $designation->update($validated);

        return redirect()->route('hr.designations.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('hr.designations.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
