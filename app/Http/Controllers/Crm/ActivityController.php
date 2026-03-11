<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmActivity;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view crm activities')->only(['index', 'show']);
        $this->middleware('can:create crm activities')->only(['create', 'store']);
        $this->middleware('can:edit crm activities')->only(['edit', 'update']);
        $this->middleware('can:delete crm activities')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = CrmActivity::with(['activitable', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $activities = $query->latest('due_date')->paginate(10);
        $users = User::active()->get();

        return view('crm.activities.index', compact('activities', 'users'));
    }

    public function create(Request $request)
    {
        $users = User::active()->get();
        $activitableType = $request->get('activitable_type');
        $activitableId = $request->get('activitable_id');

        $activitable = null;
        if ($activitableType && $activitableId) {
            if ($activitableType === 'CrmLead') {
                $activitable = CrmLead::find($activitableId);
            } elseif ($activitableType === 'CrmOpportunity') {
                $activitable = CrmOpportunity::find($activitableId);
            }
        }

        return view('crm.activities.create', compact('users', 'activitableType', 'activitableId', 'activitable'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_type' => 'required|string|max:255',
            'summary' => 'required|string',
            'due_date' => 'required|date',
            'activitable_type' => 'required|string',
            'activitable_id' => 'required|integer',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,completed,cancelled',
            'feedback' => 'nullable|string',
        ]);

        // Resolve full class name for morph
        if ($validated['activitable_type'] === 'CrmLead') {
            $validated['activitable_type'] = CrmLead::class;
        } elseif ($validated['activitable_type'] === 'CrmOpportunity') {
            $validated['activitable_type'] = CrmOpportunity::class;
        }

        $activity = CrmActivity::create($validated);

        AuditLog::log('create', 'crm_activity', $activity->id, null, $activity->toArray());

        return back()->with('success', __('messages.activity_created'));
    }

    public function update(Request $request, CrmActivity $activity)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
            'feedback' => 'nullable|string',
            'summary' => 'sometimes|required|string',
            'due_date' => 'sometimes|required|date',
        ]);

        $oldValues = $activity->toArray();
        $activity->update($validated);

        AuditLog::log('update', 'crm_activity', $activity->id, $oldValues, $activity->toArray());

        return back()->with('success', __('messages.activity_updated'));
    }

    public function destroy(CrmActivity $activity)
    {
        $oldValues = $activity->toArray();
        $activity->delete();

        AuditLog::log('delete', 'crm_activity', $activity->id, $oldValues);

        return back()->with('success', __('messages.activity_deleted'));
    }
}
