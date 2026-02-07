<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\LocalPurchase;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.view');
    }

    public function index()
    {
        return view('reports.suppliers.index');
    }

    public function byCodeOrName(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,all',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = Vendor::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ILIKE', "%{$search}%")
                  ->orWhere('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('phone', 'ILIKE', "%{$search}%");
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('is_active', $validated['status'] === 'active');
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $vendors = $query->orderBy('name')->paginate(50);

        // Load purchase summary for each vendor
        foreach ($vendors as $vendor) {
            $vendor->purchase_summary = PurchaseInvoice::where('vendor_id', $vendor->id)
                ->where('status', 'posted')
                ->select(
                    DB::raw('COUNT(*) as total_invoices'),
                    DB::raw('SUM(net_amount) as total_net'),
                    DB::raw('SUM(tax_amount) as total_tax'),
                    DB::raw('SUM(gross_amount) as total_gross')
                )
                ->first();
        }

        return view('reports.suppliers.by_code_name', compact('vendors', 'validated'));
    }

    public function localPurchases(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'status' => 'nullable|in:draft,posted,all',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $query = LocalPurchase::with(['warehouse', 'branch', 'creator']);

        if (!empty($validated['supplier_name'])) {
            $query->where('supplier_name', 'ILIKE', "%{$validated['supplier_name']}%");
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('invoice_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('invoice_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['warehouse_id'])) {
            $query->where('warehouse_id', $validated['warehouse_id']);
        }

        $purchases = $query->orderBy('invoice_date', 'desc')->paginate(50);

        // Summary statistics
        $summary = LocalPurchase::query()
            ->when(!empty($validated['supplier_name']), function ($q) use ($validated) {
                $q->where('supplier_name', 'ILIKE', "%{$validated['supplier_name']}%");
            })
            ->when(!empty($validated['date_from']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            })
            ->when(!empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            })
            ->when(!empty($validated['status']) && $validated['status'] !== 'all', function ($q) use ($validated) {
                $q->where('status', $validated['status']);
            })
            ->select(
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->first();

        return view('reports.suppliers.local_purchases', compact('purchases', 'summary', 'validated'));
    }

    public function purchaseSummary(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $query = PurchaseInvoice::with(['vendor', 'branch'])
            ->where('status', 'posted');

        if (!empty($validated['date_from'])) {
            $query->whereDate('invoice_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('invoice_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['vendor_id'])) {
            $query->where('vendor_id', $validated['vendor_id']);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(50);

        // Summary by vendor
        $vendorSummary = PurchaseInvoice::where('status', 'posted')
            ->when(!empty($validated['date_from']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            })
            ->when(!empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            })
            ->when(!empty($validated['vendor_id']), function ($q) use ($validated) {
                $q->where('vendor_id', $validated['vendor_id']);
            })
            ->select(
                'vendor_id',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross')
            )
            ->groupBy('vendor_id')
            ->with('vendor')
            ->get();

        // Overall totals
        $totals = PurchaseInvoice::where('status', 'posted')
            ->when(!empty($validated['date_from']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            })
            ->when(!empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            })
            ->when(!empty($validated['vendor_id']), function ($q) use ($validated) {
                $q->where('vendor_id', $validated['vendor_id']);
            })
            ->select(
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross')
            )
            ->first();

        return view('reports.suppliers.purchase_summary', compact('invoices', 'vendorSummary', 'totals', 'validated'));
    }

    public function exportByCodeName(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,all',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|in:csv,excel',
        ]);

        $query = Vendor::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ILIKE', "%{$search}%")
                  ->orWhere('name', 'ILIKE', "%{$search}%");
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('is_active', $validated['status'] === 'active');
        }

        $vendors = $query->orderBy('name')->get();

        $filename = 'supplier_report_' . now()->format('Y-m-d_H-i-s');

        if ($validated['format'] === 'csv') {
            return $this->exportToCsv($vendors, $filename);
        } else {
            return $this->exportToExcel($vendors, $filename);
        }
    }

    private function exportToCsv($vendors, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($vendors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Code', 'Name', 'Email', 'Phone', 'Address', 'City', 'Country', 'Tax Number', 'Status', 'Created Date']);
            
            foreach ($vendors as $vendor) {
                fputcsv($file, [
                    $vendor->code,
                    $vendor->name,
                    $vendor->email,
                    $vendor->phone,
                    $vendor->address,
                    $vendor->city,
                    $vendor->country,
                    $vendor->tax_number,
                    $vendor->is_active ? 'Active' : 'Inactive',
                    $vendor->created_at->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToExcel($vendors, $filename)
    {
        // For simplicity, using CSV format as Excel-compatible
        return $this->exportToCsv($vendors, $filename);
    }
}
