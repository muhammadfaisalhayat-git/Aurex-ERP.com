<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Branch;

class TenantController extends Controller
{
    /**
     * Switch the active company.
     */
    public function switchCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = auth()->user();

        // Only Super Admin can switch to any company
        // Company Admin can only switch to their own (redundant but safe)
        if (!$user->hasRole('Super Admin') && $user->company_id != $request->company_id) {
            abort(403, 'Unauthorized');
        }

        session(['active_company_id' => $request->company_id]);
        session()->forget('active_branch_id'); // Clear branch to force re-selection or default

        return back()->with('success', __('messages.company_switched'));
    }

    /**
     * Switch the active branch.
     */
    public function switchBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user = auth()->user();
        $branch = Branch::findOrFail($request->branch_id);

        // Ensure the branch belongs to the active company
        if ($branch->company_id != session('active_company_id')) {
            abort(403, 'Invalid branch for selected company');
        }

        // Check permissions
        if (!$user->hasRole(['Super Admin', 'Company Admin'])) {
            if ($user->branch_id != $request->branch_id) {
                abort(403, 'Unauthorized');
            }
        }

        session(['active_branch_id' => $request->branch_id]);

        return back()->with('success', __('messages.branch_switched'));
    }
}
