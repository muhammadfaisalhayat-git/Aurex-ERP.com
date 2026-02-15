<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxReportController extends Controller
{
    public function summary()
    {
        return view('reports.tax.summary');
    }

    public function byInvoice()
    {
        return view('reports.tax.by-invoice');
    }
}
