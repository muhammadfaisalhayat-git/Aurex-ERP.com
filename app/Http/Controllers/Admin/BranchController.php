<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('company')->withCount(['users', 'warehouses'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('acp.organization.branches.index', compact('branches'));
    }

    public function create()
    {
        $companies = \App\Models\Company::all();
        return view('acp.organization.branches.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:50|unique:branches,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $branch = Branch::create($validated);

        AuditLog::log('create', 'branch', $branch->id, null, $branch->toArray());

        return redirect()->route('acp.organization.branches.index')
            ->with('success', __('messages.branch_created'));
    }

    public function show(Branch $branch)
    {
        $branch->load(['users', 'warehouses', 'company']);
        return view('acp.organization.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $companies = \App\Models\Company::all();
        return view('acp.organization.branches.edit', compact('branch', 'companies'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $oldValues = $branch->toArray();
        $branch->update($validated);

        AuditLog::log('update', 'branch', $branch->id, $oldValues, $branch->toArray());

        return redirect()->route('acp.organization.branches.index')
            ->with('success', __('messages.branch_updated'));
    }

    public function destroy(Branch $branch)
    {
        if ($branch->users()->count() > 0 || $branch->warehouses()->count() > 0) {
            return back()->with('error', __('messages.cannot_delete_branch_with_relations'));
        }

        $oldValues = $branch->toArray();
        $branch->delete();

        AuditLog::log('delete', 'branch', $branch->id, $oldValues);

        return redirect()->route('acp.organization.branches.index')
            ->with('success', __('messages.branch_deleted'));
    }
}
