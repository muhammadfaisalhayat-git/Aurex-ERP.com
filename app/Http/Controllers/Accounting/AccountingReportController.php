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
        $openingBalanceQuery = LedgerEntry::where('chart_of_account_id', $account->id)
            ->where('transaction_date', '<', $request->start_date);

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
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date]);

        if ($customer) {
            $entriesQuery->where('customer_id', $customer->id);
        }
        if ($vendor) {
            $entriesQuery->where('vendor_id', $vendor->id);
        }

        $entries = $entriesQuery->orderBy('transaction_date')->get();

        return view('accounting.reports.account_statement_result', compact('account', 'entries', 'openingBalance', 'request', 'customer', 'vendor'));
    }
}
