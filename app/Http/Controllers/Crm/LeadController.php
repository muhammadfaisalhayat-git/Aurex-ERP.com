<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmLead;
use App\Models\Branch;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view crm leads')->only(['index', 'show']);
        $this->middleware('can:create crm leads')->only(['create', 'store']);
        $this->middleware('can:edit crm leads')->only(['edit', 'update']);
        $this->middleware('can:delete crm leads')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = CrmLead::with(['salesman', 'branch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->paginate(10);

        return view('crm.leads.index', compact('leads'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        $salesmen = User::active()->get();
        return view('crm.leads.create', compact('branches', 'salesmen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'salesman_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:new,contacted,qualified,converted,lost',
            'notes' => 'nullable|string',
        ]);

        $lead = CrmLead::create($validated);

        AuditLog::log('create', 'crm_lead', $lead->id, null, $lead->toArray());

        return redirect()->route('crm.leads.index')
            ->with('success', __('messages.lead_created'));
    }

    public function show(CrmLead $lead)
    {
        $lead->load(['salesman', 'branch', 'activities', 'opportunities']);
        return view('crm.leads.show', compact('lead'));
    }

    public function edit(CrmLead $lead)
    {
        $branches = Branch::active()->get();
        $salesmen = User::active()->get();
        return view('crm.leads.edit', compact('lead', 'branches', 'salesmen'));
    }

    public function update(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'salesman_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:new,contacted,qualified,converted,lost',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $lead->toArray();
        $lead->update($validated);

        AuditLog::log('update', 'crm_lead', $lead->id, $oldValues, $lead->toArray());

        return redirect()->route('crm.leads.index')
            ->with('success', __('messages.lead_updated'));
    }

    public function destroy(CrmLead $lead)
    {
        $oldValues = $lead->toArray();
        $lead->delete();

        AuditLog::log('delete', 'crm_lead', $lead->id, $oldValues);

        return redirect()->route('crm.leads.index')
            ->with('success', __('messages.lead_deleted'));
    }
}
