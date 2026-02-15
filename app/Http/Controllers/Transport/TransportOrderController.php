<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportOrderController extends Controller
{
    public function index()
    {
        return view('transport.orders.index');
    }

    public function create()
    {
        return view('transport.orders.create');
    }

    public function show($id)
    {
        return view('transport.orders.show', compact('id'));
    }
}
