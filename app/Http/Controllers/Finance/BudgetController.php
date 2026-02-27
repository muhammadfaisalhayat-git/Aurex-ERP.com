<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = \App\Models\Finance\Budget::all();
        return view('finance.budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = \App\Models\ChartOfAccount::where('is_posting_allowed', true)->get();
        return view('finance.budgets.create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'fiscal_year' => 'required|integer|min:2020|max:2100',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = session('active_company_id');
        $validated['code'] = 'BDG-' . str_pad(\App\Models\Finance\Budget::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['spent_amount'] = 0;
        $validated['status'] = 'draft';

        \App\Models\Finance\Budget::create($validated);

        return redirect()->route('finance.budgets.index')->with('success', __('messages.budget_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $budget = \App\Models\Finance\Budget::findOrFail($id);
        return view('finance.budgets.show', compact('budget'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $budget = \App\Models\Finance\Budget::findOrFail($id);
        return view('finance.budgets.edit', compact('budget'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budget = \App\Models\Finance\Budget::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'fiscal_year' => 'required|integer|min:2020|max:2100',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,active,closed',
            'notes' => 'nullable|string',
        ]);

        $budget->update($validated);

        return redirect()->route('finance.budgets.index')->with('success', __('messages.budget_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = \App\Models\Finance\Budget::findOrFail($id);
        $budget->delete();

        return redirect()->route('finance.budgets.index')->with('success', __('messages.budget_deleted_successfully'));
    }

    /**
     * Print the specified resource.
     */
    public function print(string $id)
    {
        $budget = \App\Models\Finance\Budget::findOrFail($id);
        return view('finance.budgets.print', compact('budget'));
    }
}
