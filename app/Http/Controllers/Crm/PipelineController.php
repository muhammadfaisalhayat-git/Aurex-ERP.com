<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmPipelineStage;
use App\Models\CrmOpportunity;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view crm pipeline')->only(['index']);
        $this->middleware('can:manage crm pipeline stages')->only(['stages', 'storeStage', 'updateStage', 'destroyStage', 'updateOrder']);
    }

    public function index()
    {
        $stages = CrmPipelineStage::with([
            'opportunities' => function ($query) {
                $query->with(['lead', 'customer', 'salesman'])->orderBy('updated_at', 'desc');
            }
        ])->orderBy('sort_order')->get();

        return view('crm.pipeline.index', compact('stages'));
    }

    public function updateOpportunityStage(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required|exists:crm_opportunities,id',
            'stage_id' => 'required|exists:crm_pipeline_stages,id',
        ]);

        $opportunity = CrmOpportunity::findOrFail($request->opportunity_id);
        $oldStageId = $opportunity->stage_id;

        $opportunity->update(['stage_id' => $request->stage_id]);

        AuditLog::log('update', 'crm_opportunity_stage', $opportunity->id, ['stage_id' => $oldStageId], ['stage_id' => $request->stage_id]);

        return response()->json(['success' => true]);
    }

    public function stages()
    {
        $stages = CrmPipelineStage::orderBy('sort_order')->get();
        return view('crm.pipeline.stages', compact('stages'));
    }

    public function storeStage(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $validated['sort_order'] = CrmPipelineStage::max('sort_order') + 1;

        $stage = CrmPipelineStage::create($validated);

        return redirect()->route('crm.pipeline.stages')
            ->with('success', __('messages.stage_created'));
    }

    public function updateStage(Request $request, CrmPipelineStage $stage)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $stage->update($validated);

        return redirect()->route('crm.pipeline.stages')
            ->with('success', __('messages.stage_updated'));
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:crm_pipeline_stages,id',
        ]);

        foreach ($request->order as $index => $id) {
            CrmPipelineStage::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyStage(CrmPipelineStage $stage)
    {
        if ($stage->opportunities()->exists()) {
            return back()->with('error', __('messages.cannot_delete_stage_with_opportunities'));
        }

        $stage->delete();

        return redirect()->route('crm.pipeline.stages')
            ->with('success', __('messages.stage_deleted'));
    }
}
