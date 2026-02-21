<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockReceiving;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class StockReceivingController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        return view('inventory.stock-receiving.index');
    }

    public function create()
    {
        return view('inventory.stock-receiving.create');
    }

    public function show($id)
    {
        $receiving = StockReceiving::with('items.product')->findOrFail($id);
        return view('inventory.stock-receiving.show', compact('receiving'));
    }

    public function receive($id)
    {
        $receiving = StockReceiving::findOrFail($id);

        if ($receiving->status !== 'pending') {
            return back()->with('error', 'Only pending receipts can be received.');
        }

        $receiving->update([
            'status' => 'received',
            'received_at' => now(),
            'received_by' => auth()->id(),
        ]);

        if ($this->accountingService->postStockReceiving($receiving)) {
            return back()->with('success', 'Stock receiving completed and posted to ledger.');
        }

        return back()->with('error', 'Stock receiving completed but ledger posting failed.');
    }
}
