<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function rules()
    {
        return view('sales.commissions.rules');
    }

    public function runs()
    {
        return view('sales.commissions.runs');
    }

    public function statements()
    {
        return view('sales.commissions.statements');
    }
}
