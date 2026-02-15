<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockTransferRequestController extends Controller
{
    public function index()
    {
        return view('inventory.transfer-requests.index');
    }

    public function create()
    {
        return view('inventory.transfer-requests.create');
    }

    public function show($id)
    {
        return view('inventory.transfer-requests.show', compact('id'));
    }

    public function approve($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
