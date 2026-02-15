<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockIssueOrderController extends Controller
{
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
        return view('inventory.issue-orders.show', compact('id'));
    }

    public function confirm($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
