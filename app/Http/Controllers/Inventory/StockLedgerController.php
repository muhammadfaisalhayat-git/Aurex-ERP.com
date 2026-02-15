<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockLedgerController extends Controller
{
    public function index()
    {
        return view('inventory.stock-ledger.index');
    }

    public function show($productId)
    {
        return view('inventory.stock-ledger.show', compact('productId'));
    }
}
