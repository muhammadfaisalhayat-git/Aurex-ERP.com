<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('can:manage chart of accounts');
    }

    public function index()
    {
        $accounts = ChartOfAccount::with('parent')
            ->orderBy('code')
            ->get();

        return view('accounting.coa.index', compact('accounts'));
    }

    public function create()
    {
        $parents = ChartOfAccount::where('is_active', true)->get();
        $accountTypes = AccountType::where('is_active', true)->get();
        return view('accounting.coa.create', compact('parents', 'accountTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_posting_allowed' => 'boolean',
            'sub_ledger_type' => 'nullable|string|in:customer,vendor,employee',
        ]);

        $accountType = AccountType::find($request->account_type_id);
        $code = $this->accountingService->generateAccountCode($accountType->code, $request->parent_id);

        $data = $request->all();
        $data['type'] = $accountType->code;
        $data['code'] = $code;
        $data['is_posting_allowed'] = $request->has('is_posting_allowed');

        ChartOfAccount::create($data);

        return redirect()->route('accounting.gl.coa.index')
            ->with('success', __('messages.account_created'));
    }

    public function edit(ChartOfAccount $coa)
    {
        $parents = ChartOfAccount::where('id', '!=', $coa->id)->get();
        $accountTypes = AccountType::where('is_active', true)->get();
        return view('accounting.coa.edit', compact('coa', 'parents', 'accountTypes'));
    }

    public function update(Request $request, ChartOfAccount $coa)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_posting_allowed' => 'boolean',
            'sub_ledger_type' => 'nullable|string|in:customer,vendor,employee',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['is_posting_allowed'] = $request->has('is_posting_allowed');

        $coa->update($data);

        return redirect()->route('accounting.gl.coa.index')
            ->with('success', __('messages.account_updated'));
    }

    public function destroy(ChartOfAccount $coa)
    {
        if ($coa->ledgerEntries()->exists()) {
            return back()->with('error', __('messages.cannot_delete_account_with_entries'));
        }

        $coa->delete();

        return redirect()->route('accounting.gl.coa.index')
            ->with('success', __('messages.account_deleted'));
    }
}
