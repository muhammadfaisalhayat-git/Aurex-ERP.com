<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceVoucher;
use App\Models\MaintenanceWorkshop;

class MaintenanceVoucherController extends Controller
{
    public function index()
    {
        $vouchers = MaintenanceVoucher::with('workshop')->latest()->paginate(10);
        return view('maintenance.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $workshops = MaintenanceWorkshop::where('is_active', true)->get();
        $customers = \App\Models\Customer::active()->get();
        $vendors = \App\Models\Vendor::active()->get();
        return view('maintenance.vouchers.create', compact('workshops', 'customers', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_number' => 'required|unique:maintenance_vouchers',
            'voucher_date' => 'required|date',
            'workshop_id' => 'required|exists:maintenance_workshops,id',
            'customer_id' => 'nullable|exists:customers,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'entity_type' => 'required|string',
            'entity_name' => 'required|string',
            'maintenance_type' => 'required|string',
            'problem_description' => 'required|string',
            'scheduled_date' => 'nullable|date',
            'estimated_cost' => 'nullable|numeric',
        ]);

        $validated['company_id'] = session('company_id', 1);
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        MaintenanceVoucher::create($validated);

        return redirect()->route('maintenance.vouchers.index')->with('success', 'Maintenance Voucher created successfully.');
    }

    public function show(MaintenanceVoucher $voucher)
    {
        $voucher->load(['workshop', 'parts.product', 'customer', 'vendor']);
        return view('maintenance.vouchers.show', compact('voucher'));
    }

    public function start(MaintenanceVoucher $voucher)
    {
        $voucher->update([
            'status' => 'in_progress',
            'scheduled_date' => $voucher->scheduled_date ?? now(),
        ]);

        return redirect()->back()->with('success', 'Maintenance started.');
    }

    public function complete(Request $request, MaintenanceVoucher $voucher)
    {
        $voucher->update([
            'status' => 'completed',
            'completion_date' => now(),
            'actual_cost' => $request->actual_cost,
            'work_description' => $request->work_description,
            'completed_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        // Accounting integration
        app(\App\Services\AccountingService::class)->postMaintenanceVoucher($voucher);

        return redirect()->back()->with('success', 'Maintenance completed and posted to ledger.');
    }

    public function addParts(Request $request, MaintenanceVoucher $voucher)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $voucher->parts()->create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $request->quantity * $request->unit_cost,
        ]);

        return redirect()->back()->with('success', 'Parts added to voucher.');
    }
}
