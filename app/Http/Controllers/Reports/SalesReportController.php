<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReport\SalesByItemExport;
use App\Exports\SalesReport\SalesByCustomerExport;

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
            'customer_id' => 'nullable|exists:customers,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'status' => 'nullable|in:draft,posted,cancelled,all',
        ]);

        $query = SalesInvoice::with(['customer', 'branch', 'creator']);

        if (!empty($validated['customer_id'])) {
            $query->where('customer_id', $validated['customer_id']);
        } elseif (!empty($validated['customer_code'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('code', 'LIKE', "%{$validated['customer_code']}%");
            });
        } elseif (!empty($validated['customer_name'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('name', 'LIKE', "%{$validated['customer_name']}%");
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
        $customerSummary = SalesInvoice::select(
            'customer_id',
            DB::raw('COUNT(*) as invoice_count'),
            DB::raw('SUM(total_amount) as total_net'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(subtotal) as total_gross'),
            DB::raw('SUM(discount_amount) as total_discount')
        )
            ->when(!empty($validated['customer_id']), function ($q) use ($validated) {
                $q->where('customer_id', $validated['customer_id']);
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
            ->groupBy('customer_id')
            ->with('customer')
            ->get();

        // Overall totals
        $totals = SalesInvoice::query()
            ->when(!empty($validated['customer_id']), function ($q) use ($validated) {
                $q->where('customer_id', $validated['customer_id']);
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
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(total_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(subtotal) as total_gross'),
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
            'product_id' => 'nullable|exists:products,id',
            'invoice_id' => 'nullable|exists:sales_invoices,id',
            'invoice_number' => 'nullable|string|max:50',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        $query = SalesInvoiceItem::with(['product', 'product.category', 'salesInvoice'])
            ->whereHas('salesInvoice', function ($q) {
                $q->where('status', 'posted');
            });

        if (!empty($validated['product_id'])) {
            $query->where('product_id', $validated['product_id']);
        }

        if (!empty($validated['invoice_id'])) {
            $query->where('sales_invoice_id', $validated['invoice_id']);
        }

        if (!empty($validated['item_code'])) {
            $query->whereHas('product', function ($q) use ($validated) {
                $q->where('code', 'LIKE', "%{$validated['item_code']}%");
            });
        }

        if (!empty($validated['item_name'])) {
            $query->whereHas('product', function ($q) use ($validated) {
                $q->where('name', 'LIKE', "%{$validated['item_name']}%");
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereHas('salesInvoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            });
        }

        if (!empty($validated['date_to'])) {
            $query->whereHas('salesInvoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            });
        }

        if (!empty($validated['category_id'])) {
            $query->whereHas('product', function ($q) use ($validated) {
                $q->where('category_id', $validated['category_id']);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(50);

        // Item summary
        $itemSummary = SalesInvoiceItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(net_amount) as total_net'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(gross_amount) as total_gross')
        )
            ->whereHas('salesInvoice', function ($q) use ($validated) {
                $q->where('status', 'posted');
                if (!empty($validated['invoice_id'])) {
                    $q->where('id', $validated['invoice_id']);
                }
                if (!empty($validated['date_from'])) {
                    $q->whereDate('invoice_date', '>=', $validated['date_from']);
                }
                if (!empty($validated['date_to'])) {
                    $q->whereDate('invoice_date', '<=', $validated['date_to']);
                }
            })
            ->when(!empty($validated['item_code']), function ($q) use ($validated) {
                $q->whereHas('product', function ($sq) use ($validated) {
                    $sq->where('code', 'LIKE', "%{$validated['item_code']}%");
                });
            })
            ->when(!empty($validated['item_name']), function ($q) use ($validated) {
                $q->whereHas('product', function ($sq) use ($validated) {
                    $sq->where('name', 'LIKE', "%{$validated['item_name']}%");
                });
            })
            ->groupBy('product_id')
            ->with('product')
            ->get();

        // Overall totals
        $totals = SalesInvoiceItem::whereHas('salesInvoice', function ($q) use ($validated) {
            $q->where('status', 'posted');
            if (!empty($validated['invoice_id'])) {
                $q->where('id', $validated['invoice_id']);
            }
            if (!empty($validated['date_from'])) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            }
            if (!empty($validated['date_to'])) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            }
        })
            ->when(!empty($validated['item_code']), function ($q) use ($validated) {
                $q->whereHas('product', function ($sq) use ($validated) {
                    $sq->where('code', 'LIKE', "%{$validated['item_code']}%");
                });
            })
            ->when(!empty($validated['item_name']), function ($q) use ($validated) {
                $q->whereHas('product', function ($sq) use ($validated) {
                    $sq->where('name', 'LIKE', "%{$validated['item_name']}%");
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

        $query = SalesInvoice::where('status', 'posted');

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
                DB::raw('date(invoice_date) as date'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(subtotal) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->groupBy(DB::raw('date(invoice_date)'))
            ->orderBy('date')
            ->get();

        // Monthly summary
        $monthlySummary = (clone $query)
            ->select(
                DB::raw('strftime(\'%Y-%m\', invoice_date) as month'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(subtotal) as total_gross'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->groupBy(DB::raw('strftime(\'%Y-%m\', invoice_date)'))
            ->orderBy('month')
            ->get();

        // Overall totals
        $totals = (clone $query)
            ->select(
                DB::raw('COUNT(*) as total_invoices'),
                DB::raw('SUM(total_amount) as total_net'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(subtotal) as total_gross'),
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
            'customer_id' => 'nullable|exists:customers,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|in:csv,excel,pdf',
        ]);

        $query = SalesInvoice::with('customer');

        if (!empty($validated['customer_id'])) {
            $query->where('customer_id', $validated['customer_id']);
        } elseif (!empty($validated['customer_code'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('code', 'LIKE', "%{$validated['customer_code']}%");
            });
        } elseif (!empty($validated['customer_name'])) {
            $query->whereHas('customer', function ($q) use ($validated) {
                $q->where('name', 'LIKE', "%{$validated['customer_name']}%");
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

        if ($validated['format'] === 'pdf') {
            $pdf = Pdf::loadView('reports.sales.by_customer_pdf', compact('invoices', 'validated'));
            return $pdf->download($filename . '.pdf');
        }

        if ($validated['format'] === 'excel') {
            return Excel::download(new SalesByCustomerExport($invoices), $filename . '.xlsx');
        }

        return $this->exportInvoicesToCsv($invoices, $filename);
    }

    public function exportByItem(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'nullable|string|max:50',
            'item_name' => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'invoice_id' => 'nullable|exists:sales_invoices,id',
            'invoice_number' => 'nullable|string|max:50',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|in:csv,excel,pdf',
        ]);

        $query = SalesInvoiceItem::with(['product', 'salesInvoice'])
            ->whereHas('salesInvoice', function ($q) {
                $q->where('status', 'posted');
            });

        if (!empty($validated['product_id'])) {
            $query->where('product_id', $validated['product_id']);
        }

        if (!empty($validated['invoice_id'])) {
            $query->where('sales_invoice_id', $validated['invoice_id']);
        }

        if (!empty($validated['item_code'])) {
            $query->whereHas('product', function ($q) use ($validated) {
                $q->where('code', 'LIKE', "%{$validated['item_code']}%");
            });
        }

        if (!empty($validated['item_name'])) {
            $query->whereHas('product', function ($q) use ($validated) {
                $q->where('name', 'LIKE', "%{$validated['item_name']}%");
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereHas('salesInvoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '>=', $validated['date_from']);
            });
        }

        if (!empty($validated['date_to'])) {
            $query->whereHas('salesInvoice', function ($q) use ($validated) {
                $q->whereDate('invoice_date', '<=', $validated['date_to']);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->get();

        $filename = 'sales_by_item_' . now()->format('Y-m-d_H-i-s');

        if ($validated['format'] === 'pdf') {
            // Also need totals for the PDF report
            $totals = SalesInvoiceItem::whereHas('salesInvoice', function ($q) use ($validated) {
                $q->where('status', 'posted');
                if (!empty($validated['invoice_id'])) {
                    $q->where('id', $validated['invoice_id']);
                }
                if (!empty($validated['date_from'])) {
                    $q->whereDate('invoice_date', '>=', $validated['date_from']);
                }
                if (!empty($validated['date_to'])) {
                    $q->whereDate('invoice_date', '<=', $validated['date_to']);
                }
            })
                ->when(!empty($validated['item_code']), function ($q) use ($validated) {
                    $q->whereHas('product', function ($sq) use ($validated) {
                        $sq->where('code', 'LIKE', "%{$validated['item_code']}%");
                    });
                })
                ->when(!empty($validated['item_name']), function ($q) use ($validated) {
                    $q->whereHas('product', function ($sq) use ($validated) {
                        $sq->where('name', 'LIKE', "%{$validated['item_name']}%");
                    });
                })
                ->select(
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(net_amount) as total_net'),
                    DB::raw('SUM(tax_amount) as total_tax'),
                    DB::raw('SUM(gross_amount) as total_gross')
                )
                ->first();

            $pdf = Pdf::loadView('reports.sales.by_item_pdf', compact('items', 'totals', 'validated'));
            return $pdf->download($filename . '.pdf');
        }

        if ($validated['format'] === 'excel') {
            return Excel::download(new SalesByItemExport($items), $filename . '.xlsx');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice #', 'Date', 'Item Code', 'Item Name', 'Quantity', 'Unit Price', 'Net Amount', 'Tax Amount', 'Gross Amount']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->salesInvoice->document_number ?? 'N/A',
                    optional($item->salesInvoice)->invoice_date ? $item->salesInvoice->invoice_date->format('Y-m-d') : 'N/A',
                    optional($item->product)->code ?? 'N/A',
                    optional($item->product)->name ?? 'N/A',
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
                    $invoice->invoice_date->format('Y-m-d'),
                    optional($invoice->customer)->code ?? 'N/A',
                    optional($invoice->customer)->name ?? 'N/A',
                    $invoice->subtotal,
                    $invoice->tax_amount,
                    $invoice->total_amount,
                    $invoice->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
