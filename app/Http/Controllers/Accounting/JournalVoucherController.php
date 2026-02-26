<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\ChartOfAccount;
use App\Models\Branch;
use App\Models\CostCenter;
use App\Models\Activity;
use App\Models\LetterOfCredit;
use App\Models\Promoter;
use App\Models\Employee;
use App\Services\AccountingService;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalVoucherController extends Controller
{
    protected $accountingService;
    protected $currencyService;

    public function __construct(AccountingService $accountingService, CurrencyService $currencyService)
    {
        $this->accountingService = $accountingService;
        $this->currencyService = $currencyService;
        $this->middleware('can:view journal vouchers')->only(['index', 'show']);
        $this->middleware('can:create journal vouchers')->only(['create', 'store']);
        $this->middleware('can:edit journal vouchers')->only(['edit', 'update']);
        $this->middleware('can:post journal vouchers')->only(['post']);
    }

    public function index()
    {
        $vouchers = JournalVoucher::with('creator')
            ->withSum('items', 'debit')
            ->withSum('items', 'credit')
            ->latest()
            ->paginate(10);
        return view('accounting.jv.index', compact('vouchers'));
    }

    public function create(Request $request)
    {
        $selectedAccount = null;
        if ($request->has('account_id')) {
            $selectedAccount = ChartOfAccount::with('parent')->find($request->account_id);
        }

        $mainAccounts = ChartOfAccount::main()
            ->withCount('children')
            ->orderBy('code')
            ->get();

        $branches = Branch::active()->get();
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('accounting.jv.create', compact('accounts', 'selectedAccount', 'mainAccounts', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'voucher_date' => 'required|date',
            'doc_type' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'no_of_attachments' => 'nullable|integer',
            'recipient_name' => 'nullable|string',
            'beneficiary_name' => 'nullable|string',
            'description' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.main_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
            'items.*.currency' => 'nullable|string',
            'items.*.percentage' => 'nullable|numeric',
            'items.*.cost_center_no' => 'nullable|string',
            'items.*.activity_no' => 'nullable|string',
            'items.*.lc_no' => 'nullable|string',
            'items.*.rep' => 'nullable|string',
            'items.*.collector_no' => 'nullable|string',
            'items.*.promoter_code' => 'nullable|string',
            'items.*.customer_id' => 'nullable|exists:customers,id',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
            'items.*.employee_id' => 'nullable|exists:employees,id',
        ]);

        // Validate Control Accounts
        foreach ($request->items as $index => $item) {
            $account = ChartOfAccount::find($item['chart_of_account_id']);
            if ($account && $account->sub_ledger_type === 'customer' && empty($item['customer_id'])) {
                return back()->withInput()->with('error', __('messages.customer_required_for_account') . ' ' . $account->code);
            }
            if ($account && $account->sub_ledger_type === 'vendor' && empty($item['vendor_id'])) {
                return back()->withInput()->with('error', __('messages.vendor_required_for_account') . ' ' . $account->code);
            }
        }

        // Validate balance
        $totalDebit = collect($request->items)->sum('debit');
        $totalCredit = collect($request->items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', __('messages.jv_unbalanced'));
        }

        try {
            DB::beginTransaction();

            $voucher = JournalVoucher::create([
                'voucher_number' => 'JV-' . time(),
                'voucher_date' => $request->voucher_date,
                'doc_type' => $request->doc_type ?? '1-Journal',
                'branch_name' => $request->branch_name,
                'no_of_attachments' => $request->no_of_attachments ?? 0,
                'recipient_name' => $request->recipient_name,
                'beneficiary_name' => $request->beneficiary_name,
                'description' => $request->description,
                'total_amount_text' => $this->currencyService->amountToWords($totalDebit, 'SAR', app()->getLocale()),
                'status' => $request->has('is_posted') ? 'posted' : 'draft',
                'is_posted' => $request->has('is_posted'),
                'is_reversed' => $request->has('is_reversed'),
                'is_periodic' => $request->has('is_periodic'),
                'is_currency_discrepancy' => $request->has('is_currency_discrepancy'),
                'is_suspended' => $request->has('is_suspended'),
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                // Ensure we handle sub-ledger logic
                $item['customer_id'] = !empty($item['customer_id']) ? $item['customer_id'] : null;
                $item['vendor_id'] = !empty($item['vendor_id']) ? $item['vendor_id'] : null;
                $item['employee_id'] = !empty($item['employee_id']) ? $item['employee_id'] : null;

                $voucher->items()->create($item);
            }

            if ($voucher->is_posted) {
                $this->accountingService->postJournalVoucher($voucher);
            }

            DB::commit();

            return redirect()->route('accounting.gl.transactions.jv.index')
                ->with('success', __('messages.jv_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(JournalVoucher $jv)
    {
        $jv->load(['items.account', 'items.customer', 'items.vendor']);
        $canEditPosted = auth()->user()->can('edit posted journal vouchers');

        if ($jv->status !== 'draft' && !($jv->status === 'posted' && $canEditPosted)) {
            return redirect()->route('accounting.gl.transactions.jv.show', $jv->id)
                ->with('error', __('messages.jv_not_editable'));
        }

        $mainAccounts = ChartOfAccount::main()
            ->withCount('children')
            ->orderBy('code')
            ->get();
        $branches = Branch::active()->get();
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('accounting.jv.edit', compact('jv', 'accounts', 'mainAccounts', 'branches'));
    }


    public function update(Request $request, JournalVoucher $jv)
    {
        $canEditPosted = auth()->user()->can('edit posted journal vouchers');

        if ($jv->status !== 'draft' && !($jv->status === 'posted' && $canEditPosted)) {
            return back()->with('error', __('messages.jv_not_editable'));
        }

        $request->validate([
            'voucher_date' => 'required|date',
            'doc_type' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'no_of_attachments' => 'nullable|integer',
            'recipient_name' => 'nullable|string',
            'beneficiary_name' => 'nullable|string',
            'description' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.main_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
            'items.*.currency' => 'nullable|string',
            'items.*.percentage' => 'nullable|numeric',
            'items.*.cost_center_no' => 'nullable|string',
            'items.*.activity_no' => 'nullable|string',
            'items.*.lc_no' => 'nullable|string',
            'items.*.rep' => 'nullable|string',
            'items.*.collector_no' => 'nullable|string',
            'items.*.promoter_code' => 'nullable|string',
            'items.*.customer_id' => 'nullable|exists:customers,id',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
            'items.*.employee_id' => 'nullable|exists:employees,id',
        ]);

        // Validate Control Accounts
        foreach ($request->items as $index => $item) {
            $account = ChartOfAccount::find($item['chart_of_account_id']);
            if ($account && $account->sub_ledger_type === 'customer' && empty($item['customer_id'])) {
                return back()->withInput()->with('error', __('messages.customer_required_for_account') . ' ' . $account->code);
            }
            if ($account && $account->sub_ledger_type === 'vendor' && empty($item['vendor_id'])) {
                return back()->withInput()->with('error', __('messages.vendor_required_for_account') . ' ' . $account->code);
            }
        }

        // Validate balance
        $totalDebit = collect($request->items)->sum('debit');
        $totalCredit = collect($request->items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', __('messages.jv_unbalanced'));
        }

        try {
            DB::beginTransaction();

            $jv->update([
                'voucher_date' => $request->voucher_date,
                'doc_type' => $request->doc_type ?? '1-Journal',
                'branch_name' => $request->branch_name,
                'no_of_attachments' => $request->no_of_attachments ?? 0,
                'recipient_name' => $request->recipient_name,
                'beneficiary_name' => $request->beneficiary_name,
                'description' => $request->description,
                'total_amount_text' => $this->currencyService->amountToWords($totalDebit, 'SAR', app()->getLocale()),
                'status' => $request->has('is_posted') ? 'posted' : 'draft',
                'is_posted' => $request->has('is_posted'),
                'is_reversed' => $request->has('is_reversed'),
                'is_periodic' => $request->has('is_periodic'),
                'is_currency_discrepancy' => $request->has('is_currency_discrepancy'),
                'is_suspended' => $request->has('is_suspended'),
            ]);

            // Delete existing items and recreate
            $jv->items()->delete();

            foreach ($request->items as $item) {
                $item['customer_id'] = !empty($item['customer_id']) ? $item['customer_id'] : null;
                $item['vendor_id'] = !empty($item['vendor_id']) ? $item['vendor_id'] : null;
                $item['employee_id'] = !empty($item['employee_id']) ? $item['employee_id'] : null;
                $jv->items()->create($item);
            }

            DB::commit();

            if ($jv->status === 'posted') {
                $this->accountingService->updatePostedJournalVoucher($jv->refresh());
            }

            return redirect()->route('accounting.gl.transactions.jv.show', $jv->id)
                ->with('success', __('messages.jv_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(JournalVoucher $jv)
    {
        $jv->load('items.account', 'creator');
        return view('accounting.jv.show', compact('jv'));
    }

    public function post(JournalVoucher $jv)
    {
        if ($this->accountingService->postJournalVoucher($jv)) {
            return redirect()->route('accounting.gl.transactions.jv.show', $jv->id)
                ->with('success', __('messages.jv_posted'));
        }

        return back()->with('error', __('messages.jv_posting_failed'));
    }

    public function print(JournalVoucher $jv)
    {
        $jv->load('items.account', 'creator', 'company', 'approver');
        return view('accounting.jv.print', compact('jv'));
    }

    public function ajaxSearchCostCenters(Request $request)
    {
        $q = $request->q;
        $items = CostCenter::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('code', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%")
                    ->orWhere('name_ar', 'like', "%$q%");
            })
            ->limit(20)
            ->get();

        return response()->json($items);
    }

    public function ajaxSearchActivities(Request $request)
    {
        $q = $request->q;
        $items = Activity::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('code', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%")
                    ->orWhere('name_ar', 'like', "%$q%");
            })
            ->limit(20)
            ->get();

        return response()->json($items);
    }

    public function ajaxSearchLCs(Request $request)
    {
        $q = $request->q;
        $items = LetterOfCredit::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('code', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%")
                    ->orWhere('name_ar', 'like', "%$q%");
            })
            ->limit(20)
            ->get();

        return response()->json($items);
    }

    public function ajaxSearchPromoters(Request $request)
    {
        $q = $request->q;
        $items = Promoter::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('code', 'like', "%$q%")
                    ->orWhere('name_en', 'like', "%$q%")
                    ->orWhere('name_ar', 'like', "%$q%");
            })
            ->limit(20)
            ->get();

        return response()->json($items);
    }

    public function ajaxSearchEmployees(Request $request)
    {
        $q = $request->q;
        $items = Employee::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('employee_code', 'like', "%$q%")
                    ->orWhere('first_name_en', 'like', "%$q%")
                    ->orWhere('last_name_en', 'like', "%$q%")
                    ->orWhere('first_name_ar', 'like', "%$q%")
                    ->orWhere('last_name_ar', 'like', "%$q%");
            })
            ->limit(20)
            ->get()->map(function ($e) {
                return [
                    'id' => $e->id,
                    'code' => $e->employee_code,
                    'name' => $e->first_name_en . ' ' . $e->last_name_en
                ];
            });

        return response()->json($items);
    }

    public function ajaxSearchAccounts(Request $request)
    {
        $q = $request->q;
        $companyId = session('active_company_id');

        $query = ChartOfAccount::withoutGlobalScope('tenant')
            ->where('is_active', true)
            ->where('is_posting_allowed', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $accounts = $query->where(function ($query) use ($q) {
            $query->where('code', 'like', "%$q%")
                ->orWhere('name_en', 'like', "%$q%")
                ->orWhere('name_ar', 'like', "%$q%")
                ->orWhereHas('parent', function ($query) use ($q) {
                    $query->where('name_en', 'like', "%$q%")
                        ->orWhere('name_ar', 'like', "%$q%");
                });
        })
            ->with('parent')
            ->limit(30)
            ->get();

        return response()->json($accounts->map(function ($account) {
            return [
                'id' => $account->id,
                'code' => $account->code,
                'name_en' => $account->name_en . ($account->parent ? ' (' . $account->parent->name_en . ')' : ''),
                'name_ar' => $account->name_ar,
                'type' => $account->type,
                'sub_ledger_type' => $account->sub_ledger_type,
            ];
        }));
    }

    public function ajaxAmountToWords(Request $request)
    {
        $amount = $request->amount;
        $currency = $request->currency ?? 'SAR';

        return response()->json([
            'words_en' => $this->currencyService->amountToWords($amount, $currency, 'en'),
            'words_ar' => $this->currencyService->amountToWords($amount, $currency, 'ar'),
        ]);
    }
}
