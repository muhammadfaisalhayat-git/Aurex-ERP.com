<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = \App\Models\Finance\FixedAsset::with('category')->get();
        return view('finance.fixed_assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Finance\AssetCategory::where('is_active', true)->get();
        $accounts = \App\Models\ChartOfAccount::where('is_posting_allowed', true)->get();
        return view('finance.fixed_assets.create', compact('categories', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|in:straight_line,declining_balance',
            'asset_account_id' => 'required|exists:chart_of_accounts,id',
            'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        $validated['company_id'] = session('active_company_id');
        $validated['branch_id'] = session('active_branch_id');
        $validated['code'] = 'AST-' . str_pad(\App\Models\Finance\FixedAsset::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['current_value'] = $request->purchase_cost;
        $validated['status'] = 'active';

        \App\Models\Finance\FixedAsset::create($validated);

        return redirect()->route('finance.fixed-assets.index')->with('success', __('messages.asset_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = \App\Models\Finance\FixedAsset::with(['category', 'assetAccount'])->findOrFail($id);
        return view('finance.fixed_assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = \App\Models\Finance\FixedAsset::findOrFail($id);
        $categories = \App\Models\Finance\AssetCategory::where('is_active', true)->get();
        $accounts = \App\Models\ChartOfAccount::where('is_posting_allowed', true)->get();
        return view('finance.fixed_assets.edit', compact('asset', 'categories', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = \App\Models\Finance\FixedAsset::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|in:straight_line,declining_balance',
            'asset_account_id' => 'required|exists:chart_of_accounts,id',
            'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
            'status' => 'required|in:active,disposed,written_off',
        ]);

        $asset->update($validated);

        return redirect()->route('finance.fixed-assets.index')->with('success', __('messages.asset_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = \App\Models\Finance\FixedAsset::findOrFail($id);
        $asset->delete();

        return redirect()->route('finance.fixed-assets.index')->with('success', __('messages.asset_deleted_successfully'));
    }

    /**
     * Print the specified resource.
     */
    public function print(string $id)
    {
        $asset = \App\Models\Finance\FixedAsset::with(['category', 'assetAccount'])->findOrFail($id);
        return view('finance.fixed_assets.print', compact('asset'));
    }
}
