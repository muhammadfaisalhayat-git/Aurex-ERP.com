<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index()
    {
        return view('inventory.stock-transfers.index');
    }

    public function create()
    {
        return view('inventory.stock-transfers.create');
    }

    public function show($id)
    {
        return view('inventory.stock-transfers.show', compact('id'));
    }

    public function execute($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
