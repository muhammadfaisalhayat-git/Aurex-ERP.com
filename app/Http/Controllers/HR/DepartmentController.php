<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view departments')->only(['index', 'show']);
        $this->middleware('permission:manage departments')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $departments = Department::with('branch')->paginate(20);
        return view('hr.departments.index', compact('departments'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        return view('hr.departments.create', compact('branches'));
    }

    public function show(Department $department)
    {
        $department->load(['designations', 'employees']);
        return view('hr.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $branches = Branch::active()->get();
        return view('hr.departments.edit', compact('department', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Department::create($validated);

        return redirect()->route('hr.departments.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('hr.departments.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('hr.departments.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
