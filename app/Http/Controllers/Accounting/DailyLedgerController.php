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
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DailyLedgerExport;

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
        $data = $this->getLedgerData($request);

        return response()->json([
            'entries' => $data['entries'],
            'opening_balance' => $data['opening_balance'],
            'total_debit' => $data['total_debit'],
            'total_credit' => $data['total_credit'],
            'net_movement' => $data['net_movement']
        ]);
    }

    private function getLedgerData(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::today()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');

        $query = LedgerEntry::with(['chartOfAccount', 'customer', 'vendor', 'branch', 'employee'])
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

        // Fetch Company and Branch for header
        $company = \App\Models\Company::find(Session::get('active_company_id'))
            ?? \App\Models\Company::first();

        $branch = null;
        if ($request->branch_id) {
            $branch = \App\Models\Branch::find($request->branch_id);
        }
        elseif (Session::has('active_branch_id')) {
            $branch = \App\Models\Branch::find(Session::get('active_branch_id'));
        }

        return [
            'entries' => $entries,
            'opening_balance' => $openingBalance,
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'net_movement' => $entries->sum('debit') - $entries->sum('credit'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company' => $company,
            'branch' => $branch
        ];
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getLedgerData($request);
        $pdf = Pdf::loadView('accounting.reports.pdf.daily_ledger', $data);
        return $pdf->download('daily_ledger_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getLedgerData($request);
        return Excel::download(new DailyLedgerExport($data), 'daily_ledger_' . now()->format('Y-m-d') . '.xlsx');
    }
}
