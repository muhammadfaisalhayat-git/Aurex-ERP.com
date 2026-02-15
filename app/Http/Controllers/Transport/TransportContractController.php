<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportContractController extends Controller
{
    public function index()
    {
        return view('transport.contracts.index');
    }

    public function show($id)
    {
        return view('transport.contracts.show', compact('id'));
    }
}
