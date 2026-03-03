<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxReportController extends Controller
{
    public function summary(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $salesTax = \App\Models\SalesInvoice::whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->where('status', 'posted')
            ->sum('tax_amount');

        $purchaseTax = \App\Models\PurchaseInvoice::whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->where('status', 'posted')
            ->sum('tax_amount');

        $netTax = $salesTax - $purchaseTax;

        return view('reports.tax.summary', compact('salesTax', 'purchaseTax', 'netTax', 'year', 'month'));
    }

    public function byInvoice(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $salesInvoices = \App\Models\SalesInvoice::with('customer')
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->where('status', 'posted')
            ->get();

        $purchaseInvoices = \App\Models\PurchaseInvoice::with('vendor')
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->where('status', 'posted')
            ->get();

        return view('reports.tax.by-invoice', compact('salesInvoices', 'purchaseInvoices', 'year', 'month'));
    }
}
