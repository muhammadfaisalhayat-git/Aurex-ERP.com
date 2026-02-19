<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Today's Stats
        $stats = LedgerEntry::whereDate('transaction_date', $today)
            ->select(
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->first();

        $netMovement = ($stats->total_debit ?? 0) - ($stats->total_credit ?? 0);

        // Top 5 Active Accounts (by volume of transactions today)
        $topAccounts = LedgerEntry::whereDate('transaction_date', $today)
            ->select('chart_of_account_id', DB::raw('SUM(debit + credit) as volume'))
            ->with('chartOfAccount')
            ->groupBy('chart_of_account_id')
            ->orderByDesc('volume')
            ->limit(5)
            ->get();

        // Top 5 Customers Movement
        $topCustomers = LedgerEntry::whereDate('transaction_date', $today)
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('SUM(debit - credit) as movement'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc(DB::raw('ABS(SUM(debit - credit))'))
            ->limit(5)
            ->get();

        // Top 5 Suppliers Movement
        $topSuppliers = LedgerEntry::whereDate('transaction_date', $today)
            ->whereNotNull('vendor_id')
            ->select('vendor_id', DB::raw('SUM(debit - credit) as movement'))
            ->with('vendor')
            ->groupBy('vendor_id')
            ->orderByDesc(DB::raw('ABS(SUM(debit - credit))'))
            ->limit(5)
            ->get();

        return view('accounting.dashboard', compact('stats', 'netMovement', 'topAccounts', 'topCustomers', 'topSuppliers'));
    }
}
