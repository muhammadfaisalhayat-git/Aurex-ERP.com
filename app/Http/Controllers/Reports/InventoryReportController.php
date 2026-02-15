<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function valuation()
    {
        return view('reports.inventory.valuation');
    }

    public function movements()
    {
        return view('reports.inventory.movements');
    }

    public function lowStock()
    {
        return view('reports.inventory.low-stock');
    }
}
