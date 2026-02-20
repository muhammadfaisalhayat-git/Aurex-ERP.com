<?php

namespace App\Http\Controllers\Accounting\Setup;

use App\Http\Controllers\Controller;
use App\Models\LetterOfCredit;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LetterOfCreditController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view lcs')->only(['index', 'show']);
        $this->middleware('can:create lcs')->only(['create', 'store']);
        $this->middleware('can:edit lcs')->only(['edit', 'update']);
        $this->middleware('can:delete lcs')->only(['destroy']);
    }

    public function index()
    {
        $items = LetterOfCredit::orderBy('code')->paginate(15);
        return view('accounting.setup.lcs.index', compact('items'));
    }

    public function create()
    {
        return view('accounting.setup.lcs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:letter_of_credits,code',
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

            $item = LetterOfCredit::create($validated);

            AuditLog::log('create', 'lc', $item->id, null, $item->toArray());

            return redirect()->route('accounting.gl.setup.lcs.index')
                ->with('success', __('messages.lc_created') ?: 'LC created successfully.');
        }
        catch (\Exception $e) {
            \Log::error('LC Save Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error saving LC: ' . $e->getMessage());
        }
    }

    public function edit(LetterOfCredit $lc)
    {
        return view('accounting.setup.lcs.edit', ['item' => $lc]);
    }

    public function update(Request $request, LetterOfCredit $lc)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:letter_of_credits,code,' . $lc->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $oldValues = $lc->toArray();
        $lc->update($validated);

        AuditLog::log('update', 'lc', $lc->id, $oldValues, $lc->toArray());

        return redirect()->route('accounting.gl.setup.lcs.index')
            ->with('success', __('messages.lc_updated'));
    }

    public function destroy(LetterOfCredit $lc)
    {
        $oldValues = $lc->toArray();
        $lc->delete();

        AuditLog::log('delete', 'lc', $lc->id, $oldValues);

        return redirect()->route('accounting.setup.lcs.index')
            ->with('success', __('messages.lc_deleted'));
    }
}
