<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function valuation()
    {
        $products = \App\Models\Product::with(['stockBalances.warehouse', 'category'])->get();
        return view('reports.inventory.valuation', compact('products'));
    }

    public function movements(Request $request)
    {
        $query = \App\Models\StockLedger::with(['product', 'warehouse']);

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $movements = $query->orderBy('transaction_date', 'desc')->get();
        $products = \App\Models\Product::all();
        $warehouses = \App\Models\Warehouse::all();

        return view('reports.inventory.movements', compact('movements', 'products', 'warehouses', 'request'));
    }

    public function lowStock()
    {
        $products = \App\Models\Product::whereHas('stockBalances', function ($q) {
            $q->select('product_id')
                ->groupBy('product_id')
                ->havingRaw('SUM(available_quantity) <= products.reorder_level');
        })->orWhereRaw('reorder_level > 0 AND NOT EXISTS (SELECT 1 FROM stock_balances WHERE stock_balances.product_id = products.id)')
            ->with('stockBalances')
            ->get();

        return view('reports.inventory.low-stock', compact('products'));
    }
}
