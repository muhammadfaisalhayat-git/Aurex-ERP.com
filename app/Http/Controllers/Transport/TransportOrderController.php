<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportOrderController extends Controller
{
    public function index()
    {
        $orders = \App\Models\TransportOrder::with(['trailer', 'vehicle', 'driver'])->paginate(10);
        return view('transport.orders.index', compact('orders'));
    }

    public function create()
    {
        $trailers = \App\Models\Trailer::active()->available()->get();
        $vehicles = \App\Models\Logistics\DeliveryVehicle::where('status', 'available')->get();
        $drivers = \App\Models\User::all(); // Simplified, usually filtered by role
        $branches = \App\Models\Branch::all();
        $products = \App\Models\Product::all();

        return view('transport.orders.create', compact('trailers', 'vehicles', 'drivers', 'branches', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|unique:transport_orders,document_number',
            'order_date' => 'required|date',
            'trailer_id' => 'nullable|exists:trailers,id',
            'delivery_vehicle_id' => 'nullable|exists:delivery_vehicles,id',
            'driver_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'route_from' => 'required|string',
            'route_to' => 'required|string',
            'scheduled_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        \DB::transaction(function () use ($validated) {
            $order = \App\Models\TransportOrder::create(\Illuminate\Support\Arr::except($validated, ['items']));
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }
        });

        return redirect()->route('transport.orders.index')
            ->with('success', __('messages.order_created_successfully'));
    }

    public function show(\App\Models\TransportOrder $order)
    {
        $order->load(['trailer', 'vehicle', 'driver', 'items.product', 'branch']);
        return view('transport.orders.show', compact('order'));
    }

    public function edit(\App\Models\TransportOrder $order)
    {
        $order->load('items');
        $trailers = \App\Models\Trailer::active()->get();
        $vehicles = \App\Models\Logistics\DeliveryVehicle::all();
        $drivers = \App\Models\User::all();
        $branches = \App\Models\Branch::all();
        $products = \App\Models\Product::all();

        return view('transport.orders.edit', compact('order', 'trailers', 'vehicles', 'drivers', 'branches', 'products'));
    }

    public function update(Request $request, \App\Models\TransportOrder $order)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|unique:transport_orders,document_number,' . $order->id,
            'order_date' => 'required|date',
            'trailer_id' => 'nullable|exists:trailers,id',
            'delivery_vehicle_id' => 'nullable|exists:delivery_vehicles,id',
            'driver_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'route_from' => 'required|string',
            'route_to' => 'required|string',
            'scheduled_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.notes' => 'nullable|string',
        ]);

        \DB::transaction(function () use ($order, $validated) {
            $order->update(\Illuminate\Support\Arr::except($validated, ['items']));
            $order->items()->delete();
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }
        });

        return redirect()->route('transport.orders.index')
            ->with('success', __('messages.order_updated_successfully'));
    }

    public function destroy(\App\Models\TransportOrder $order)
    {
        $order->items()->delete();
        $order->delete();
        return redirect()->route('transport.orders.index')
            ->with('success', __('messages.order_deleted_successfully'));
    }

    public function confirm(\App\Models\TransportOrder $order)
    {
        $order->update(['status' => 'confirmed']);
        return back()->with('success', __('messages.order_confirmed_successfully'));
    }

    public function complete(\App\Models\TransportOrder $order)
    {
        $order->update([
            'status' => 'completed',
            'completion_date' => now(),
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);
        return back()->with('success', __('messages.order_completed_successfully'));
    }
}
