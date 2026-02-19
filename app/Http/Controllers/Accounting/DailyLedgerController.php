<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyLedgerController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        $accounts = ChartOfAccount::orderBy('code')->get();
        $customers = Customer::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('accounting.reports.daily_ledger', compact('branches', 'accounts', 'customers', 'vendors'));
    }

    public function fetch(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::today()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');

        $query = LedgerEntry::with(['chartOfAccount', 'customer', 'vendor', 'branch'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->chart_of_account_id) {
            $query->where('chart_of_account_id', $request->chart_of_account_id);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Calculate Opening Balance
        $openingBalanceQuery = LedgerEntry::where('transaction_date', '<', $startDate);

        if ($request->branch_id) {
            $openingBalanceQuery->where('branch_id', $request->branch_id);
        }
        if ($request->chart_of_account_id) {
            $openingBalanceQuery->where('chart_of_account_id', $request->chart_of_account_id);
        }
        if ($request->customer_id) {
            $openingBalanceQuery->where('customer_id', $request->customer_id);
        }
        if ($request->vendor_id) {
            $openingBalanceQuery->where('vendor_id', $request->vendor_id);
        }

        $openingBalance = $openingBalanceQuery->sum(DB::raw('debit - credit'));

        $entries = $query->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $runningBalance = $openingBalance;
        foreach ($entries as $entry) {
            $runningBalance += ($entry->debit - $entry->credit);
            $entry->running_balance = $runningBalance;
        }

        return response()->json([
            'entries' => $entries,
            'opening_balance' => $openingBalance,
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'net_movement' => $entries->sum('debit') - $entries->sum('credit')
        ]);
    }

    public function exportPdf(Request $request)
    {
        // Similar logic to fetch but returning PDF
        // Placeholder for now, will implement if specified view is ready
    }

    public function exportExcel(Request $request)
    {
        // Similar logic to fetch but returning Excel
        // Placeholder for now
    }
}
