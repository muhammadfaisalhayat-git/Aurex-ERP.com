<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierReportController extends Controller
{
    public function index()
    {
        return view('reports.suppliers.index');
    }

    public function byCodeOrName(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:all,active,inactive',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Vendor::query()
            ->withCount('purchaseInvoices')
            ->withSum('purchaseInvoices', 'total_amount');

        // Search filter
        if (!empty($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('code', 'like', '%' . $validated['search'] . '%')
                    ->orWhere('name_en', 'like', '%' . $validated['search'] . '%')
                    ->orWhere('name_ar', 'like', '%' . $validated['search'] . '%')
                    ->orWhere('email', 'like', '%' . $validated['search'] . '%');
            });
        }

        // Status filter
        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        // Date range filter
        if (!empty($validated['date_from']) || !empty($validated['date_to'])) {
            $query->whereBetween('created_at', [
                $validated['date_from'] ?? '1970-01-01',
                $validated['date_to'] ?? now()->format('Y-m-d')
            ]);
        }

        $vendors = $query->orderBy('code')->paginate(20)->appends($validated);

        return view('reports.suppliers.by_code_name', compact('vendors', 'validated'));
    }

    public function localPurchases(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:all,draft,posted',
        ]);

        // Base query
        $query = \App\Models\PurchaseInvoice::query()->with(['vendor', 'warehouse']);

        // Apply filters
        if (!empty($validated['supplier_name'])) {
            $query->whereHas('vendor', function ($q) use ($validated) {
                $q->where('name_en', 'like', '%' . $validated['supplier_name'] . '%')
                    ->orWhere('name_ar', 'like', '%' . $validated['supplier_name'] . '%');
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['date_from']) || !empty($validated['date_to'])) {
            $query->whereBetween('invoice_date', [
                $validated['date_from'] ?? '1970-01-01',
                $validated['date_to'] ?? now()->format('Y-m-d')
            ]);
        }

        // Calculate summary
        $summary = $query->selectRaw('
            COUNT(*) as total_count,
            SUM(subtotal) as total_net,
            SUM(tax_amount) as total_tax,
            SUM(total_amount) as total_gross
        ')->first();

        // Get purchases with pagination
        $purchases = \App\Models\PurchaseInvoice::query()
            ->with(['vendor', 'warehouse'])
            ->when(!empty($validated['supplier_name']), function ($q) use ($validated) {
                $q->whereHas('vendor', function ($vendorQuery) use ($validated) {
                    $vendorQuery->where('name_en', 'like', '%' . $validated['supplier_name'] . '%')
                        ->orWhere('name_ar', 'like', '%' . $validated['supplier_name'] . '%');
                });
            })
            ->when(!empty($validated['status']) && $validated['status'] !== 'all', function ($q) use ($validated) {
                $q->where('status', $validated['status']);
            })
            ->when(!empty($validated['date_from']) || !empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereBetween('invoice_date', [
                    $validated['date_from'] ?? '1970-01-01',
                    $validated['date_to'] ?? now()->format('Y-m-d')
                ]);
            })
            ->orderByDesc('invoice_date')
            ->paginate(20)
            ->appends($validated);

        return view('reports.suppliers.local_purchases', compact('validated', 'summary', 'purchases'));
    }

    public function purchaseSummary(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // Base query for purchase invoices
        $query = \App\Models\PurchaseInvoice::query();

        // Apply filters
        if (!empty($validated['vendor_id'])) {
            $query->where('vendor_id', $validated['vendor_id']);
        }

        if (!empty($validated['date_from']) || !empty($validated['date_to'])) {
            $query->whereBetween('invoice_date', [
                $validated['date_from'] ?? '1970-01-01',
                $validated['date_to'] ?? now()->format('Y-m-d')
            ]);
        }

        // Calculate totals
        $totals = $query->selectRaw('
            COUNT(*) as total_invoices,
            SUM(subtotal) as total_net,
            SUM(tax_amount) as total_tax,
            SUM(total_amount) as total_gross
        ')->first();

        // Get vendor summary
        $vendorSummary = \App\Models\PurchaseInvoice::query()
            ->when(!empty($validated['vendor_id']), function ($q) use ($validated) {
                $q->where('vendor_id', $validated['vendor_id']);
            })
            ->when(!empty($validated['date_from']) || !empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereBetween('invoice_date', [
                    $validated['date_from'] ?? '1970-01-01',
                    $validated['date_to'] ?? now()->format('Y-m-d')
                ]);
            })
            ->with('vendor')
            ->selectRaw('vendor_id, COUNT(*) as invoice_count, SUM(total_amount) as total_gross')
            ->groupBy('vendor_id')
            ->orderByDesc('total_gross')
            ->limit(10)
            ->get();

        // Get invoice details with pagination
        $invoices = \App\Models\PurchaseInvoice::query()
            ->when(!empty($validated['vendor_id']), function ($q) use ($validated) {
                $q->where('vendor_id', $validated['vendor_id']);
            })
            ->when(!empty($validated['date_from']) || !empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereBetween('invoice_date', [
                    $validated['date_from'] ?? '1970-01-01',
                    $validated['date_to'] ?? now()->format('Y-m-d')
                ]);
            })
            ->with('vendor')
            ->orderByDesc('invoice_date')
            ->paginate(15)
            ->appends($validated);

        return view('reports.suppliers.purchase_summary', compact('validated', 'totals', 'vendorSummary', 'invoices'));
    }

    public function exportByCodeName(Request $request)
    {
        // TODO: Implement CSV export
        return back()->with('info', 'Export feature coming soon');
    }
}
