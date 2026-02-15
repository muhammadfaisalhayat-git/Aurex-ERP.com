<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
    public function index()
    {
        return view('transport.trailers.index');
    }

    public function create()
    {
        return view('transport.trailers.create');
    }
}
