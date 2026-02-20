<?php

namespace App\Http\Controllers\Accounting\Setup;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view cost_centers')->only(['index', 'show']);
        $this->middleware('can:create cost_centers')->only(['create', 'store']);
        $this->middleware('can:edit cost_centers')->only(['edit', 'update']);
        $this->middleware('can:delete cost_centers')->only(['destroy']);
    }

    public function index()
    {
        $items = CostCenter::orderBy('code')->paginate(15);
        return view('accounting.setup.cost-centers.index', compact('items'));
    }

    public function create()
    {
        return view('accounting.setup.cost-centers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:cost_centers,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        try {
            $validated['company_id'] = session('active_company_id') ?: auth()->user()->company_id;
            $validated['branch_id'] = session('active_branch_id') ?: auth()->user()->branch_id;
            $validated['is_active'] = $request->has('is_active');

            if (!$validated['company_id']) {
                return redirect()->back()->withInput()->with('error', 'Unable to determine active company. Please select a company first.');
            }

            $item = CostCenter::create($validated);

            AuditLog::log('create', 'cost_center', $item->id, null, $item->toArray());

            return redirect()->route('accounting.gl.setup.cost-centers.index')
                ->with('success', __('messages.cost_center_created') ?: 'Cost center created successfully.');
        }
        catch (\Exception $e) {
            \Log::error('Cost Center Save Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error saving cost center: ' . $e->getMessage());
        }
    }

    public function edit(CostCenter $costCenter)
    {
        return view('accounting.setup.cost-centers.edit', ['item' => $costCenter]);
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:cost_centers,code,' . $costCenter->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $oldValues = $costCenter->toArray();
        $costCenter->update($validated);

        AuditLog::log('update', 'cost_center', $costCenter->id, $oldValues, $costCenter->toArray());

        return redirect()->route('accounting.gl.setup.cost-centers.index')
            ->with('success', __('messages.cost_center_updated'));
    }

    public function destroy(CostCenter $costCenter)
    {
        $oldValues = $costCenter->toArray();
        $costCenter->delete();

        AuditLog::log('delete', 'cost_center', $costCenter->id, $oldValues);

        return redirect()->route('accounting.setup.cost-centers.index')
            ->with('success', __('messages.cost_center_deleted'));
    }
}
