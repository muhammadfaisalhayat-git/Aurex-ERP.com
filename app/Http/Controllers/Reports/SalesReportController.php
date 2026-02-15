<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.view');
    }

    public function index()
    {
        return view('reports.sales.index');
    }

    public function byCustomer(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'nullable|string|max:50',
            'customer_name' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'status' => 'nullable|in:draft,posted,cancelled,all',
        ]);

        $query = Invoice::with(['customer', 'branch', 'creator']);

        if (!empty($validated['customer_code'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('code', 'ILIKE', "%{$validated['customer_code']}%");
            });
        }

        if (!empty($validated['customer_name'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('name', 'ILIKE', "%{$validated['customer_name']}%");
            });
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

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(50);

        // Customer summary
        $customerSummary = Invoice::select(
            'customer_id',
            DB::raw('COUNT(*) as invoice_count'),
            DB::raw('SUM(net_amount) as total_net'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(gross_amount) as total_gross'),
            DB::raw('SUM(discount_amount) as total_discount')
        )
            ->when(!empty($validated['date_from']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            })
            ->when(!empty($validated['date_to']), function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            })
            ->when(!empty($validated['status']) && $validated['status'] !== 'all', function ($q) use ($validated) {
                $q->where('status', $validated['status']);
            })
            ->groupBy('customer_id')
            ->with('customer')
            ->get();

        // Overall totals
        $totals = Invoice::query()
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
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->first();

        return view('reports.sales.by_customer', compact('invoices', 'customerSummary', 'totals', 'validated'));
    }

    public function byItem(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'nullable|string|max:50',
            'item_name' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        $query = InvoiceItem::with(['item', 'item.category', 'invoice'])
            ->whereHas('invoice', function ($q) {
                $q->where('status', 'posted');
            });

        if (!empty($validated['item_code'])) {
            $query->whereHas('item', function ($q) use ($validated) {
                $q->where('code', 'ILIKE', "%{$validated['item_code']}%");
            });
        }

        if (!empty($validated['item_name'])) {
            $query->whereHas('item', function ($q) use ($validated) {
                $q->where('name', 'ILIKE', "%{$validated['item_name']}%");
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereHas('invoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            });
        }

        if (!empty($validated['date_to'])) {
            $query->whereHas('invoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            });
        }

        if (!empty($validated['category_id'])) {
            $query->whereHas('item', function ($q) use ($validated) {
                $q->where('category_id', $validated['category_id']);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(50);

        // Item summary
        $itemSummary = InvoiceItem::select(
            'item_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(net_amount) as total_net'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(gross_amount) as total_gross')
        )
            ->whereHas('invoice', function ($q) use ($validated) {
                $q->where('status', 'posted');
                if (!empty($validated['date_from'])) {
                    $q->whereDate('invoice_date', '>=', $validated['date_from']);
                }
                if (!empty($validated['date_to'])) {
                    $q->whereDate('invoice_date', '<=', $validated['date_to']);
                }
            })
            ->when(!empty($validated['item_code']), function ($q) use ($validated) {
                $q->whereHas('item', function ($sq) use ($validated) {
                    $sq->where('code', 'ILIKE', "%{$validated['item_code']}%");
                });
            })
            ->when(!empty($validated['item_name']), function ($q) use ($validated) {
                $q->whereHas('item', function ($sq) use ($validated) {
                    $sq->where('name', 'ILIKE', "%{$validated['item_name']}%");
                });
            })
            ->groupBy('item_id')
            ->with('item')
            ->get();

        // Overall totals
        $totals = InvoiceItem::whereHas('invoice', function ($q) use ($validated) {
            $q->where('status', 'posted');
            if (!empty($validated['date_from'])) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            }
            if (!empty($validated['date_to'])) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            }
        })
            ->when(!empty($validated['item_code']), function ($q) use ($validated) {
                $q->whereHas('item', function ($sq) use ($validated) {
                    $sq->where('code', 'ILIKE', "%{$validated['item_code']}%");
                });
            })
            ->when(!empty($validated['item_name']), function ($q) use ($validated) {
                $q->whereHas('item', function ($sq) use ($validated) {
                    $sq->where('name', 'ILIKE', "%{$validated['item_name']}%");
                });
            })
            ->select(
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross')
            )
            ->first();

        $categories = \App\Models\ProductCategory::where('is_active', true)->get();

        return view('reports.sales.by_item', compact('items', 'itemSummary', 'totals', 'categories', 'validated'));
    }

    public function dateWise(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'group_by' => 'nullable|in:day,week,month,year',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $groupBy = $validated['group_by'] ?? 'day';

        $query = Invoice::where('status', 'posted');

        if (!empty($validated['date_from'])) {
            $query->whereDate('invoice_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('invoice_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['branch_id'])) {
            $query->where('branch_id', $validated['branch_id']);
        }

        // Daily summary
        $dailySummary = (clone $query)
            ->select(
                DB::raw('DATE(invoice_date) as date'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->groupBy(DB::raw('DATE(invoice_date)'))
            ->orderBy('date')
            ->get();

        // Monthly summary
        $monthlySummary = (clone $query)
            ->select(
                DB::raw('DATE_TRUNC(\'month\', invoice_date) as month'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->groupBy(DB::raw('DATE_TRUNC(\'month\', invoice_date)'))
            ->orderBy('month')
            ->get();

        // Overall totals
        $totals = (clone $query)
            ->select(
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->first();

        $branches = \App\Models\Branch::where('is_active', true)->get();

        return view('reports.sales.date_wise', compact('dailySummary', 'monthlySummary', 'totals', 'branches', 'validated'));
    }

    public function exportByCustomer(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'nullable|string|max:50',
            'customer_name' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|in:csv,excel',
        ]);

        $query = Invoice::with('customer');

        if (!empty($validated['customer_code'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('code', 'ILIKE', "%{$validated['customer_code']}%");
            });
        }

        if (!empty($validated['customer_name'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('name', 'ILIKE', "%{$validated['customer_name']}%");
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('invoice_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('invoice_date', '<=', $validated['date_to']);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();

        $filename = 'sales_by_customer_' . now()->format('Y-m-d_H-i-s');

        return $this->exportInvoicesToCsv($invoices, $filename);
    }

    public function exportByItem(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'nullable|string|max:50',
            'item_name' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|in:csv,excel',
        ]);

        $query = InvoiceItem::with(['item', 'invoice'])
            ->whereHas('invoice', function ($q) {
                $q->where('status', 'posted');
            });

        if (!empty($validated['item_code'])) {
            $query->whereHas('item', function ($q) use ($validated) {
                $q->where('code', 'ILIKE', "%{$validated['item_code']}%");
            });
        }

        if (!empty($validated['item_name'])) {
            $query->whereHas('item', function ($q) use ($validated) {
                $q->where('name', 'ILIKE', "%{$validated['item_name']}%");
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereHas('invoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            });
        }

        if (!empty($validated['date_to'])) {
            $query->whereHas('invoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->get();

        $filename = 'sales_by_item_' . now()->format('Y-m-d_H-i-s');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice #', 'Date', 'Item Code', 'Item Name', 'Quantity', 'Unit Price', 'Net Amount', 'Tax Amount', 'Gross Amount']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->invoice->document_number,
                    $item->invoice->invoice_date,
                    $item->item->code,
                    $item->item->name,
                    $item->quantity,
                    $item->unit_price,
                    $item->net_amount,
                    $item->tax_amount,
                    $item->gross_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportInvoicesToCsv($invoices, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice #', 'Date', 'Customer Code', 'Customer Name', 'Net Amount', 'Tax Amount', 'Gross Amount', 'Status']);

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->document_number,
                    $invoice->invoice_date,
                    $invoice->customer->code,
                    $invoice->customer->name,
                    $invoice->net_amount,
                    $invoice->tax_amount,
                    $invoice->gross_amount,
                    $invoice->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
