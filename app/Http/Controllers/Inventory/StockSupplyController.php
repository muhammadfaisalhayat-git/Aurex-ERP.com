<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockSupplyController extends Controller
{
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
        return view('inventory.stock-supply.show', compact('id'));
    }

    public function confirm($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
