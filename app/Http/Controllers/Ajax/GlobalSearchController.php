<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\LocalPurchase;
use App\Models\Employee;
use App\Models\Department;
use App\Models\ChartOfAccount;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\SupplyOrder;
use App\Models\TransportOrder;
use App\Models\MaintenanceVoucher;
use App\Models\Production\WorkCenter;
use App\Models\Production\Machine;
use App\Models\Production\ProductionOrder;
use App\Models\Logistics\DeliveryVehicle;
use App\Models\Logistics\FuelLog;
use App\Models\JournalVoucher;
use App\Models\StockTransfer;
use App\Models\Trailer;
use Illuminate\Support\Facades\Log;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $q = $request->get('q');
            if (!$q || strlen($q) < 2) {
                return response()->json([]);
            }

            $results = [];
            $locale = app()->getLocale();

            // 1. Customers
            $customers = Customer::where('name_en', 'like', "%$q%")
                ->orWhere('name_ar', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($customers as $c) {
                $results[] = [
                    'type' => __('messages.customer'),
                    'title' => ($locale == 'ar' ? $c->name_ar : $c->name_en) ?: $c->name_en,
                    'subtitle' => $c->code . ($c->address ? " | " . $c->address : ""),
                    'url' => route('sales.customers.show', $c->id),
                    'icon' => 'fas fa-user-tie'
                ];
            }

            // 2. Vendors
            $vendors = Vendor::where('name_en', 'like', "%$q%")
                ->orWhere('name_ar', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($vendors as $v) {
                $results[] = [
                    'type' => __('messages.vendor'),
                    'title' => ($locale == 'ar' ? $v->name_ar : $v->name_en) ?: $v->name_en,
                    'subtitle' => $v->code . ($v->phone ? " | " . $v->phone : ""),
                    'url' => route('purchases.vendors.show', $v->id),
                    'icon' => 'fas fa-truck'
                ];
            }

            // 3. Products
            $products = Product::where('name_en', 'like', "%$q%")
                ->orWhere('name_ar', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->orWhere('sku', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($products as $p) {
                $results[] = [
                    'type' => __('messages.product'),
                    'title' => ($locale == 'ar' ? $p->name_ar : $p->name_en) ?: $p->name_en,
                    'subtitle' => $p->sku ?: $p->code,
                    'url' => route('inventory.products.show', $p->id),
                    'icon' => 'fas fa-box'
                ];
            }

            // 4. Sales Invoices
            $sInvoices = SalesInvoice::where('invoice_number', 'like', "%$q%")
                ->orWhere('document_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($sInvoices as $si) {
                $results[] = [
                    'type' => __('messages.sales_invoice'),
                    'title' => $si->invoice_number ?: $si->document_number,
                    'subtitle' => ($si->invoice_date ? $si->invoice_date->format('Y-m-d') : ''),
                    'url' => route('sales.invoices.show', $si->id),
                    'icon' => 'fas fa-file-invoice-dollar'
                ];
            }

            // 5. Purchase Invoices
            $pInvoices = PurchaseInvoice::where('invoice_number', 'like', "%$q%")
                ->orWhere('document_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($pInvoices as $pi) {
                $results[] = [
                    'type' => __('messages.purchase_invoice'),
                    'title' => $pi->invoice_number ?: $pi->document_number,
                    'subtitle' => ($pi->invoice_date ? $pi->invoice_date->format('Y-m-d') : ''),
                    'url' => route('purchases.invoices.show', $pi->id),
                    'icon' => 'fas fa-file-alt'
                ];
            }

            // 6. Local Purchases
            $lPurchases = LocalPurchase::where('invoice_number', 'like', "%$q%")
                ->orWhere('document_number', 'like', "%$q%")
                ->orWhere('supplier_name', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($lPurchases as $lp) {
                $results[] = [
                    'type' => __('messages.local_purchase') ?? 'Local Purchase',
                    'title' => $lp->invoice_number ?: $lp->document_number,
                    'subtitle' => $lp->supplier_name,
                    'url' => route('purchases.local-purchases.show', $lp->id),
                    'icon' => 'fas fa-shopping-basket'
                ];
            }

            // 7. Quotations
            $quotations = Quotation::where('document_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($quotations as $qn) {
                $results[] = [
                    'type' => __('messages.quotation'),
                    'title' => $qn->document_number,
                    'subtitle' => ($qn->quotation_date ? $qn->quotation_date->format('Y-m-d') : ''),
                    'url' => route('sales.quotations.show', $qn->id),
                    'icon' => 'fas fa-file-signature'
                ];
            }

            // 8. Sales Orders
            $sOrders = SalesOrder::where('document_number', 'like', "%$q%")
                ->orWhere('order_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($sOrders as $so) {
                $results[] = [
                    'type' => __('messages.sales_order'),
                    'title' => $so->document_number ?: $so->order_number,
                    'subtitle' => ($so->order_date ? $so->order_date->format('Y-m-d') : ''),
                    'url' => route('sales.sales-orders.show', $so->id),
                    'icon' => 'fas fa-shopping-cart'
                ];
            }

            // 9. Supply Orders
            $spOrders = SupplyOrder::where('document_number', 'like', "%$q%")
                ->orWhere('order_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($spOrders as $spo) {
                $results[] = [
                    'type' => __('messages.supply_order'),
                    'title' => $spo->document_number ?: $spo->order_number,
                    'subtitle' => ($spo->order_date ? $spo->order_date->format('Y-m-d') : ''),
                    'url' => route('purchases.supply-orders.show', $spo->id),
                    'icon' => 'fas fa-clipboard-list'
                ];
            }

            // 10. Transport Orders
            $tOrders = TransportOrder::where('document_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($tOrders as $to) {
                $results[] = [
                    'type' => __('messages.transport_order'),
                    'title' => $to->document_number,
                    'subtitle' => ($to->order_date ? $to->order_date->format('Y-m-d') : ''),
                    'url' => route('transport.orders.show', $to->id),
                    'icon' => 'fas fa-shipping-fast'
                ];
            }

            // 11. Maintenance Vouchers
            $mVouchers = MaintenanceVoucher::where('voucher_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($mVouchers as $mv) {
                $results[] = [
                    'type' => __('messages.maintenance_voucher'),
                    'title' => $mv->voucher_number,
                    'url' => route('maintenance.vouchers.show', $mv->id),
                    'icon' => 'fas fa-tools',
                    'subtitle' => ($mv->voucher_date ? $mv->voucher_date->format('Y-m-d') : '')
                ];
            }

            // 12. Production Orders
            $pOrders = ProductionOrder::where('document_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($pOrders as $po) {
                $results[] = [
                    'type' => __('messages.production_order') ?? 'Production Order',
                    'title' => $po->document_number,
                    'subtitle' => ($po->start_date ? $po->start_date->format('Y-m-d') : ''),
                    'url' => route('production.orders.show', $po->id),
                    'icon' => 'fas fa-industry'
                ];
            }

            // 13. Vehicles
            $vehicles = DeliveryVehicle::where('plate_number', 'like', "%$q%")
                ->orWhere('brand', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($vehicles as $vec) {
                $results[] = [
                    'type' => __('messages.vehicle') ?? 'Vehicle',
                    'title' => $vec->plate_number,
                    'subtitle' => $vec->brand . ' ' . $vec->model,
                    'url' => route('logistics.vehicles.show', $vec->id),
                    'icon' => 'fas fa-truck-moving'
                ];
            }

            // 14. Trailers
            $trailers = Trailer::where('plate_number', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($trailers as $tr) {
                $results[] = [
                    'type' => __('messages.trailer') ?? 'Trailer',
                    'title' => $tr->plate_number ?: $tr->code,
                    'subtitle' => $tr->trailer_type,
                    'url' => route('logistics.trailers.show', $tr->id),
                    'icon' => 'fas fa-trailer'
                ];
            }

            // 15. Machines
            $machines = Machine::where('name', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($machines as $m) {
                $results[] = [
                    'type' => __('messages.machine') ?? 'Machine',
                    'title' => $m->name,
                    'subtitle' => $m->code,
                    'url' => route('production.setup.machines.index', ['search' => $m->code]),
                    'icon' => 'fas fa-cogs'
                ];
            }

            // 16. Fuel Logs
            $fuelLogs = FuelLog::where('fuel_station', 'like', "%$q%")
                ->orWhere('odometer_reading', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($fuelLogs as $fl) {
                $results[] = [
                    'type' => __('messages.fuel_log') ?? 'Fuel Log',
                    'title' => $fl->fuel_station,
                    'subtitle' => ($fl->entry_date ? $fl->entry_date->format('Y-m-d') : '') . " | " . $fl->liters . "L",
                    'url' => route('logistics.fuel-logs.index', ['search' => $fl->fuel_station]),
                    'icon' => 'fas fa-gas-pump'
                ];
            }

            // 17. Employees
            $employees = Employee::where('first_name_en', 'like', "%$q%")
                ->orWhere('last_name_en', 'like', "%$q%")
                ->orWhere('first_name_ar', 'like', "%$q%")
                ->orWhere('last_name_ar', 'like', "%$q%")
                ->orWhere('employee_code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($employees as $e) {
                $results[] = [
                    'type' => __('messages.employee'),
                    'title' => $e->name,
                    'subtitle' => $e->employee_code . ($e->department ? ' | ' . $e->department->name : ''),
                    'url' => route('hr.employees.show', $e->id),
                    'icon' => 'fas fa-id-card'
                ];
            }

            // 17. Departments
            $departments = Department::where('name_en', 'like', "%$q%")
                ->orWhere('name_ar', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($departments as $dept) {
                $results[] = [
                    'type' => __('messages.department') ?? 'Department',
                    'title' => ($locale == 'ar' ? $dept->name_ar : $dept->name_en) ?: $dept->name_en,
                    'subtitle' => $dept->code,
                    'url' => route('hr.departments.show', $dept->id),
                    'icon' => 'fas fa-sitemap'
                ];
            }

            // 18. Journal Vouchers
            $jVouchers = JournalVoucher::where('voucher_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($jVouchers as $jv) {
                $results[] = [
                    'type' => __('messages.journal_voucher'),
                    'title' => $jv->voucher_number,
                    'subtitle' => ($jv->voucher_date ? $jv->voucher_date->format('Y-m-d') : ''),
                    'url' => route('accounting.transactions.jv.show', $jv->id),
                    'icon' => 'fas fa-receipt'
                ];
            }

            // 19. Stock Transfers
            $transfers = StockTransfer::where('document_number', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($transfers as $st) {
                $results[] = [
                    'type' => __('messages.stock_transfer') ?? 'Stock Transfer',
                    'title' => $st->document_number,
                    'subtitle' => ($st->transfer_date ? $st->transfer_date->format('Y-m-d') : ''),
                    'url' => route('inventory.stock-transfers.show', $st->id),
                    'icon' => 'fas fa-exchange-alt'
                ];
            }

            // 20. Accounts
            $accounts = ChartOfAccount::where('name_en', 'like', "%$q%")
                ->orWhere('name_ar', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->limit(5)->get();
            foreach ($accounts as $a) {
                $results[] = [
                    'type' => __('messages.account'),
                    'title' => ($locale == 'ar' ? $a->name_ar : $a->name_en) ?: $a->name_en,
                    'subtitle' => $a->code,
                    'url' => route('accounting.gl.coa.index', ['search' => $a->code]),
                    'icon' => 'fas fa-book'
                ];
            }

            // 21. App Pages (Searchable Functions)
            $appPages = $this->getAppPages();
            foreach ($appPages as $page) {
                $matches = false;
                if (str_contains(strtolower($page['title']), strtolower($q))) {
                    $matches = true;
                }
                if (!$matches && isset($page['keywords'])) {
                    foreach ($page['keywords'] as $keyword) {
                        if (str_contains(strtolower($keyword), strtolower($q))) {
                            $matches = true;
                            break;
                        }
                    }
                }

                if ($matches) {
                    $results[] = [
                        'type' => __('messages.customize') ?? 'Page',
                        'title' => $page['title'],
                        'subtitle' => $page['subtitle'] ?? '',
                        'url' => $page['url'],
                        'icon' => $page['icon']
                    ];
                }
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Global Search Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getAppPages()
    {
        $locale = app()->getLocale();

        return [
            // Sales
            [
                'title' => __('messages.customers'),
                'url' => route('sales.customers.index'),
                'icon' => 'fas fa-user-tie',
                'keywords' => ['customers', 'العملاء', 'sales', 'مبيعات']
            ],
            [
                'title' => __('messages.quotations'),
                'url' => route('sales.quotations.index'),
                'icon' => 'fas fa-file-signature',
                'keywords' => ['quotations', 'عروض الأسعار', 'sales', 'مبيعات']
            ],
            [
                'title' => __('messages.sales_orders'),
                'url' => route('sales.sales-orders.index'),
                'icon' => 'fas fa-shopping-cart',
                'keywords' => ['sales orders', 'أوامر البيع', 'sales', 'مبيعات']
            ],
            [
                'title' => __('messages.sales_invoices'),
                'url' => route('sales.invoices.index'),
                'icon' => 'fas fa-file-invoice-dollar',
                'keywords' => ['sales invoices', 'فواتير المبيعات', 'sales', 'مبيعات', 'invoices', 'فواتير']
            ],
            [
                'title' => __('messages.sales_returns'),
                'url' => route('sales.returns.index'),
                'icon' => 'fas fa-undo',
                'keywords' => ['sales returns', 'مرتجعات المبيعات', 'sales', 'مبيعات']
            ],

            // Purchases
            [
                'title' => __('messages.vendors'),
                'url' => route('purchases.vendors.index'),
                'icon' => 'fas fa-truck',
                'keywords' => ['vendors', 'الموردين', 'purchases', 'مشتريات']
            ],
            [
                'title' => __('messages.supply_orders'),
                'url' => route('purchases.supply-orders.index'),
                'icon' => 'fas fa-clipboard-list',
                'keywords' => ['supply orders', 'أوامر التوريد', 'purchases', 'مشتريات']
            ],
            [
                'title' => __('messages.purchase_invoices'),
                'url' => route('purchases.invoices.index'),
                'icon' => 'fas fa-shopping-basket',
                'keywords' => ['purchase invoices', 'فواتير المشتريات', 'purchases', 'مشتريات', 'invoices', 'فواتير']
            ],
            [
                'title' => __('messages.local_purchases'),
                'url' => route('purchases.local-purchases.index'),
                'icon' => 'fas fa-store',
                'keywords' => ['local purchases', 'مشتريات محلية', 'purchases', 'مشتريات']
            ],

            // Inventory
            [
                'title' => __('messages.products'),
                'url' => route('inventory.products.index'),
                'icon' => 'fas fa-box',
                'keywords' => ['products', 'المنتجات', 'inventory', 'مخزون']
            ],
            [
                'title' => __('messages.categories'),
                'url' => route('inventory.categories.index'),
                'icon' => 'fas fa-tags',
                'keywords' => ['categories', 'الفئات', 'inventory', 'مخزون']
            ],
            [
                'title' => __('messages.stock_transfers'),
                'url' => route('inventory.stock-transfers.index'),
                'icon' => 'fas fa-exchange-alt',
                'keywords' => ['stock transfers', 'تحويلات مخزنية', 'inventory', 'مخزون']
            ],
            [
                'title' => __('messages.stock_ledger'),
                'url' => route('inventory.stock-ledger.index'),
                'icon' => 'fas fa-list-alt',
                'keywords' => ['stock ledger', 'دفتر المخزون', 'inventory', 'مخزون']
            ],

            // HR
            [
                'title' => __('messages.employees'),
                'url' => route('hr.employees.index'),
                'icon' => 'fas fa-users',
                'keywords' => ['employees', 'الموظفين', 'hr', 'موظفين']
            ],
            [
                'title' => __('messages.departments'),
                'url' => route('hr.departments.index'),
                'icon' => 'fas fa-sitemap',
                'keywords' => ['departments', 'الأقسام', 'hr', 'موظفين']
            ],

            // Accounting
            [
                'title' => __('messages.chart_of_accounts'),
                'url' => route('accounting.gl.coa.index'),
                'icon' => 'fas fa-book',
                'keywords' => ['chart of accounts', 'شجرة الحسابات', 'accounting', 'محاسبة']
            ],
            [
                'title' => __('messages.journal_vouchers'),
                'url' => route('accounting.gl.transactions.jv.index'),
                'icon' => 'fas fa-exchange-alt',
                'keywords' => ['journal vouchers', 'قيود اليومية', 'accounting', 'محاسبة', 'jv']
            ],

            // Logistics
            [
                'title' => __('messages.delivery_vehicles'),
                'url' => route('logistics.vehicles.index'),
                'icon' => 'fas fa-truck-moving',
                'keywords' => ['vehicles', 'مركبات', 'logistics', 'نقل']
            ],
            [
                'title' => __('messages.fuel_logs'),
                'url' => route('logistics.fuel-logs.index'),
                'icon' => 'fas fa-gas-pump',
                'keywords' => ['fuel logs', 'سجلات الوقود', 'logistics', 'نقل']
            ],

            // Maintenance
            [
                'title' => __('messages.maintenance_vouchers'),
                'url' => route('maintenance.vouchers.index'),
                'icon' => 'fas fa-tools',
                'keywords' => ['maintenance', 'صيانة', 'vouchers']
            ],
        ];
    }
}
