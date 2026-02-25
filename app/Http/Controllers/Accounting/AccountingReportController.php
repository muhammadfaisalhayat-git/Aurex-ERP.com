<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view accounting reports');
    }

    public function accountStatement()
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        return view('accounting.reports.account_statement', compact('accounts'));
    }

    public function generateAccountStatement(Request $request)
    {
        $request->validate([
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'customer_id' => 'nullable|exists:customers,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $account = ChartOfAccount::findOrFail($request->chart_of_account_id);
        $customer = $request->customer_id ? \App\Models\Customer::find($request->customer_id) : null;
        $vendor = $request->vendor_id ? \App\Models\Vendor::find($request->vendor_id) : null;

        // Calculate Opening Balance
        $startDate = \Illuminate\Support\Carbon::parse($request->start_date)->startOfDay();
        $endDate = \Illuminate\Support\Carbon::parse($request->end_date)->endOfDay();

        $openingBalanceQuery = LedgerEntry::where('chart_of_account_id', $account->id)
            ->where('transaction_date', '<', $startDate);

        if ($customer) {
            $openingBalanceQuery->where('customer_id', $customer->id);
        }
        if ($vendor) {
            $openingBalanceQuery->where('vendor_id', $vendor->id);
        }

        $openingBalance = $openingBalanceQuery->sum(DB::raw('debit - credit'));

        // Fetch Entries
        $entriesQuery = LedgerEntry::with(['customer', 'vendor']) // Eager load for display
            ->where('chart_of_account_id', $account->id)
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($customer) {
            $entriesQuery->where('customer_id', $customer->id);
        }
        if ($vendor) {
            $entriesQuery->where('vendor_id', $vendor->id);
        }

        $entries = $entriesQuery->orderBy('transaction_date')->get();

        return view('accounting.reports.account_statement_result', compact('account', 'entries', 'openingBalance', 'request', 'customer', 'vendor'));
    }

    public function trialBalance(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $accounts = ChartOfAccount::posting()
            ->withSum([
                'ledgerEntries as total_debit' => function ($q) use ($date) {
                    $q->where('transaction_date', '<=', $date);
                }
            ], 'debit')
            ->withSum([
                'ledgerEntries as total_credit' => function ($q) use ($date) {
                    $q->where('transaction_date', '<=', $date);
                }
            ], 'credit')
            ->orderBy('code')
            ->get();

        return view('accounting.reports.trial_balance', compact('accounts', 'date'));
    }

    public function profitAndLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $revenues = ChartOfAccount::where('type', 'revenue')
            ->withSum([
                'ledgerEntries as balance' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                }
            ], DB::raw('credit - debit'))
            ->get();

        $expenses = ChartOfAccount::where('type', 'expense')
            ->withSum([
                'ledgerEntries as balance' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                }
            ], DB::raw('debit - credit'))
            ->get();

        return view('accounting.reports.profit_loss', compact('revenues', 'expenses', 'startDate', 'endDate'));
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $assets = ChartOfAccount::where('type', 'asset')
            ->withSum([
                'ledgerEntries as balance' => function ($q) use ($date) {
                    $q->where('transaction_date', '<=', $date);
                }
            ], DB::raw('debit - credit'))
            ->get();

        $liabilities = ChartOfAccount::where('type', 'liability')
            ->withSum([
                'ledgerEntries as balance' => function ($q) use ($date) {
                    $q->where('transaction_date', '<=', $date);
                }
            ], DB::raw('credit - debit'))
            ->get();

        $equity = ChartOfAccount::where('type', 'equity')
            ->withSum([
                'ledgerEntries as balance' => function ($q) use ($date) {
                    $q->where('transaction_date', '<=', $date);
                }
            ], DB::raw('credit - debit'))
            ->get();

        return view('accounting.reports.balance_sheet', compact('assets', 'liabilities', 'equity', 'date'));
    }
}
