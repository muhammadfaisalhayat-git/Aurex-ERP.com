<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountExplorerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view accounting reports');
    }

    public function index()
    {
        $mainAccounts = ChartOfAccount::main()
            ->withCount('children')
            ->orderBy('code')
            ->get();

        return view('accounting.explorer.index', compact('mainAccounts'));
    }

    public function getSubAccounts(ChartOfAccount $account)
    {
        $subAccounts = $account->children()
            ->withCount('children')
            ->orderBy('code')
            ->get();

        return response()->json($subAccounts);
    }

    public function getAccountData(ChartOfAccount $account, Request $request)
    {
        $query = LedgerEntry::where('chart_of_account_id', $account->id);

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $entries = $query->with(['customer', 'vendor'])
            ->orderBy('transaction_date', 'desc')
            ->limit(100)
            ->get();

        $summary = [
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'balance' => $entries->sum('debit') - $entries->sum('credit'),
            'count' => $entries->count()
        ];

        return response()->json([
            'account' => $account->load('accountType'),
            'entries' => $entries,
            'summary' => $summary
        ]);
    }
}