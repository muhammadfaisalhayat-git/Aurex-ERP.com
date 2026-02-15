<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierReportController extends Controller
{
    public function index()
    {
        return view('reports.suppliers.index');
    }

    public function byCodeOrName()
    {
        return view('reports.suppliers.by-code-name');
    }

    public function localPurchases()
    {
        return view('reports.suppliers.local-purchases');
    }

    public function purchaseSummary()
    {
        return view('reports.suppliers.purchase-summary');
    }
}
