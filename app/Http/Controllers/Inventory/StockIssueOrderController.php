<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockIssueOrder;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class StockIssueOrderController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        return view('inventory.issue-orders.index');
    }

    public function create()
    {
        return view('inventory.issue-orders.create');
    }

    public function show($id)
    {
        $issueOrder = StockIssueOrder::with('items.product')->findOrFail($id);
        return view('inventory.issue-orders.show', compact('issueOrder'));
    }

    public function confirm($id)
    {
        $issueOrder = StockIssueOrder::findOrFail($id);

        if ($issueOrder->status !== 'draft') {
            return back()->with('error', 'Only draft issue orders can be confirmed.');
        }

        $issueOrder->update([
            'status' => 'confirmed',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        if ($this->accountingService->postStockIssue($issueOrder)) {
            return back()->with('success', 'Stock issue confirmed and posted to ledger.');
        }

        return back()->with('error', 'Stock issue confirmed but ledger posting failed.');
    }
}
