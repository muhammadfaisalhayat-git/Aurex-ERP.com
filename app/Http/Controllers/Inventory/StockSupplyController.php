<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockSupply;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class StockSupplyController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        return view('inventory.stock-supply.index');
    }

    public function create()
    {
        return view('inventory.stock-supply.create');
    }

    public function show($id)
    {
        $supply = StockSupply::with('items.product')->findOrFail($id);
        return view('inventory.stock-supply.show', compact('supply'));
    }

    public function confirm($id)
    {
        $supply = StockSupply::findOrFail($id);

        if ($supply->status !== 'draft') {
            return back()->with('error', 'Only draft supplies can be confirmed.');
        }

        $supply->update([
            'status' => 'confirmed',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        if ($this->accountingService->postStockSupply($supply)) {
            return back()->with('success', 'Stock supply confirmed and posted to ledger.');
        }

        return back()->with('error', 'Stock supply confirmed but ledger posting failed.');
    }
}
