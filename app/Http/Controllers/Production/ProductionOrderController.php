<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\ProductionOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductionOrderController extends Controller
{
    public function index()
    {
        $orders = ProductionOrder::with('product')->latest()->paginate(10);
        return view('production.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::all();
        return view('production.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|unique:production_orders,document_number',
            'product_id' => 'required|exists:products,id',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
            'quantity' => 'required|numeric|min:0.001',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = Session::get('active_company_id');
        $validated['branch_id'] = Session::get('active_branch_id');
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        ProductionOrder::create($validated);

        return redirect()->route('production.orders.index')
            ->with('success', 'Production Order created successfully.');
    }

    public function show(ProductionOrder $order)
    {
        $order->load(['product', 'workOrders.workCenter', 'workOrders.machine', 'qualityControls.inspector']);
        return view('production.orders.show', compact('order'));
    }

    public function post(ProductionOrder $order)
    {
        if ($order->post()) {
            return redirect()->back()->with('success', 'Production Order completed and posted to ledger.');
        }

        return redirect()->back()->with('error', 'Production Order could not be posted.');
    }
}
