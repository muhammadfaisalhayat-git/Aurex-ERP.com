<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Branch;

class TaxReportController extends Controller
{
    public function summary()
    {
        $branches = Branch::all();
        return view('reports.tax.summary', compact('branches'));
    }

    public function byInvoice()
    {
        $branches = Branch::all();
        return view('reports.tax.by-invoice', compact('branches'));
    }
}
