<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function index()
    {
        return view('sales.returns.index');
    }

    public function create()
    {
        return view('sales.returns.create');
    }

    public function show($id)
    {
        return view('sales.returns.show', compact('id'));
    }
}
