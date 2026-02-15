<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportClaimController extends Controller
{
    public function index()
    {
        return view('transport.claims.index');
    }

    public function create()
    {
        return view('transport.claims.create');
    }

    public function show($id)
    {
        return view('transport.claims.show', compact('id'));
    }
}
