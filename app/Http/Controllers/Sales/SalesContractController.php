<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesContractController extends Controller
{
    public function index()
    {
        return view('sales.contracts.index');
    }

    public function create()
    {
        return view('sales.contracts.create');
    }

    public function show($id)
    {
        return view('sales.contracts.show', compact('id'));
    }

    public function print($id)
    {
        return back()->with('info', __('messages.feature_coming_soon'));
    }
}
