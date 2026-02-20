<?php

namespace App\Http\Controllers\Accounting\Setup;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view activities')->only(['index', 'show']);
        $this->middleware('can:create activities')->only(['create', 'store']);
        $this->middleware('can:edit activities')->only(['edit', 'update']);
        $this->middleware('can:delete activities')->only(['destroy']);
    }

    public function index()
    {
        $items = Activity::orderBy('code')->paginate(15);
        return view('accounting.setup.activities.index', compact('items'));
    }

    public function create()
    {
        return view('accounting.setup.activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:activities,code',
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

            $item = Activity::create($validated);

            AuditLog::log('create', 'activity', $item->id, null, $item->toArray());

            return redirect()->route('accounting.gl.setup.activities.index')
                ->with('success', __('messages.activity_created') ?: 'Activity created successfully.');
        }
        catch (\Exception $e) {
            \Log::error('Activity Save Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error saving activity: ' . $e->getMessage());
        }
    }

    public function edit(Activity $activity)
    {
        return view('accounting.setup.activities.edit', ['item' => $activity]);
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:activities,code,' . $activity->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $oldValues = $activity->toArray();
        $activity->update($validated);

        AuditLog::log('update', 'activity', $activity->id, $oldValues, $activity->toArray());

        return redirect()->route('accounting.gl.setup.activities.index')
            ->with('success', __('messages.activity_updated'));
    }

    public function destroy(Activity $activity)
    {
        $oldValues = $activity->toArray();
        $activity->delete();

        AuditLog::log('delete', 'activity', $activity->id, $oldValues);

        return redirect()->route('accounting.setup.activities.index')
            ->with('success', __('messages.activity_deleted'));
    }
}
