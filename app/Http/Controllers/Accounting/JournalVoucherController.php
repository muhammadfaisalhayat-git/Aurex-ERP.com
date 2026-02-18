<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalVoucherController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('can:view journal vouchers')->only(['index', 'show']);
        $this->middleware('can:create journal vouchers')->only(['create', 'store']);
        $this->middleware('can:edit journal vouchers')->only(['edit', 'update']);
        $this->middleware('can:post journal vouchers')->only(['post']);
    }

    public function index()
    {
        $vouchers = JournalVoucher::with('creator')->latest()->paginate(10);
        return view('accounting.jv.index', compact('vouchers'));
    }

    public function create()
    {
        $mainAccounts = ChartOfAccount::main()->where('is_active', true)->orderBy('code')->get();
        // Fetch all active accounts for the initial load, or we can fetch via AJAX. 
        // For simplicity and existing pattern, let's pass all, but UI will likely need a better structure or AJAX.
        // Actually, the requirement is "Select Main -> Select Sub".
        // Let's pass main accounts and a way to get subs. 
        // To avoid N+1 and huge payloads, maybe we just pass everything and filter in JS, 
        // or just pass main accounts and use AJAX for subs. 
        // Given the previous code just sent all accounts, let's send all but organized or just all and let JS handle.
        // Let's stick to the current pattern but maybe enhance it. 
        // The user wants "Select Main Account" then "Select Sub Account". 

        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('accounting.jv.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'voucher_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.main_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
            'items.*.customer_id' => 'nullable|exists:customers,id',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
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

        if ($totalDebit != $totalCredit) {
            return back()->withInput()->with('error', __('messages.jv_unbalanced'));
        }

        try {
            DB::beginTransaction();

            $voucher = JournalVoucher::create([
                'voucher_number' => 'JV-' . time(),
                'voucher_date' => $request->voucher_date,
                'description' => $request->description,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                // Ensure we handle sub-ledger logic
                $item['customer_id'] = !empty($item['customer_id']) ? $item['customer_id'] : null;
                $item['vendor_id'] = !empty($item['vendor_id']) ? $item['vendor_id'] : null;

                $voucher->items()->create($item);
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
        $canEditPosted = auth()->user()->can('edit posted journal vouchers');

        if ($jv->status !== 'draft' && !($jv->status === 'posted' && $canEditPosted)) {
            return redirect()->route('accounting.gl.transactions.jv.show', $jv->id)
                ->with('error', __('messages.jv_not_editable'));
        }

        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        return view('accounting.jv.edit', compact('jv', 'accounts'));
    }

    public function update(Request $request, JournalVoucher $jv)
    {
        $canEditPosted = auth()->user()->can('edit posted journal vouchers');

        if ($jv->status !== 'draft' && !($jv->status === 'posted' && $canEditPosted)) {
            return back()->with('error', __('messages.jv_not_editable'));
        }

        $request->validate([
            'voucher_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.main_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
            'items.*.customer_id' => 'nullable|exists:customers,id',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
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
                'description' => $request->description,
            ]);

            // Delete existing items and recreate
            $jv->items()->delete();

            foreach ($request->items as $item) {
                $item['customer_id'] = !empty($item['customer_id']) ? $item['customer_id'] : null;
                $item['vendor_id'] = !empty($item['vendor_id']) ? $item['vendor_id'] : null;
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
}
