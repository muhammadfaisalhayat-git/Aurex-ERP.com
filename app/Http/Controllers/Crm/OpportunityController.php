<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmOpportunity;
use App\Models\CrmLead;
use App\Models\CrmPipelineStage;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view crm opportunities')->only(['index', 'show']);
        $this->middleware('can:create crm opportunities')->only(['create', 'store']);
        $this->middleware('can:edit crm opportunities')->only(['edit', 'update']);
        $this->middleware('can:delete crm opportunities')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = CrmOpportunity::with(['lead', 'customer', 'stage', 'salesman']);

        if ($request->filled('stage_id')) {
            $query->where('stage_id', $request->stage_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $opportunities = $query->latest()->paginate(10);
        $stages = CrmPipelineStage::orderBy('sort_order')->get();

        return view('crm.opportunities.index', compact('opportunities', 'stages'));
    }

    public function create(Request $request)
    {
        $leads = CrmLead::all();
        $customers = Customer::active()->get();
        $stages = CrmPipelineStage::orderBy('sort_order')->get();
        $branches = Branch::active()->get();
        $salesmen = User::active()->get();

        $selectedLeadId = $request->get('lead_id');

        return view('crm.opportunities.create', compact('leads', 'customers', 'stages', 'branches', 'salesmen', 'selectedLeadId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'expected_revenue' => 'required|numeric|min:0',
            'expected_closing' => 'nullable|date',
            'probability' => 'required|integer|min:0|max:100',
            'stage_id' => 'required|exists:crm_pipeline_stages,id',
            'salesman_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string',
        ]);

        $opportunity = CrmOpportunity::create($validated);

        AuditLog::log('create', 'crm_opportunity', $opportunity->id, null, $opportunity->toArray());

        return redirect()->route('crm.opportunities.index')
            ->with('success', __('messages.opportunity_created'));
    }

    public function show(CrmOpportunity $opportunity)
    {
        $opportunity->load(['lead', 'customer', 'stage', 'salesman', 'activities']);
        return view('crm.opportunities.show', compact('opportunity'));
    }

    public function edit(CrmOpportunity $opportunity)
    {
        $leads = CrmLead::all();
        $customers = Customer::active()->get();
        $stages = CrmPipelineStage::orderBy('sort_order')->get();
        $branches = Branch::active()->get();
        $salesmen = User::active()->get();

        return view('crm.opportunities.edit', compact('opportunity', 'leads', 'customers', 'stages', 'branches', 'salesmen'));
    }

    public function update(Request $request, CrmOpportunity $opportunity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lead_id' => 'nullable|exists:crm_leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'expected_revenue' => 'required|numeric|min:0',
            'expected_closing' => 'nullable|date',
            'probability' => 'required|integer|min:0|max:100',
            'stage_id' => 'required|exists:crm_pipeline_stages,id',
            'salesman_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string',
        ]);

        $oldValues = $opportunity->toArray();
        $opportunity->update($validated);

        AuditLog::log('update', 'crm_opportunity', $opportunity->id, $oldValues, $opportunity->toArray());

        return redirect()->route('crm.opportunities.index')
            ->with('success', __('messages.opportunity_updated'));
    }

    public function destroy(CrmOpportunity $opportunity)
    {
        $oldValues = $opportunity->toArray();
        $opportunity->delete();

        AuditLog::log('delete', 'crm_opportunity', $opportunity->id, $oldValues);

        return redirect()->route('crm.opportunities.index')
            ->with('success', __('messages.opportunity_deleted'));
    }
}
