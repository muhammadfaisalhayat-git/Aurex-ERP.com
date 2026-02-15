<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockReceivingController extends Controller
{
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
        return view('inventory.stock-receiving.show', compact('id'));
    }

    public function receive($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
