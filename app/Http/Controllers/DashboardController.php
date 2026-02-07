<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesReturn;
use App\Models\StockTransferRequest;
use App\Models\DashboardWidget;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get user's dashboard widgets
        $widgets = DashboardWidget::where('user_id', $user->id)
            ->where('is_visible', true)
            ->orderBy('position_y')
            ->orderBy('position_x')
            ->get();

        // If no widgets configured, create defaults
        if ($widgets->isEmpty()) {
            $widgets = $this->createDefaultWidgets($user);
        }

        $widgetData = [];
        foreach ($widgets as $widget) {
            $widgetData[$widget->widget_type] = $this->getWidgetData($widget->widget_type);
        }

        // Get recent audit logs for Super Admin
        $recentActivities = null;
        if ($user->isSuperAdmin()) {
            $recentActivities = AuditLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Get pending approvals
        $pendingApprovals = $this->getPendingApprovals($user);

        return view('dashboard', compact('widgets', 'widgetData', 'recentActivities', 'pendingApprovals'));
    }

    public function reorderWidgets(Request $request)
    {
        $request->validate([
            'widgets' => 'required|array',
            'widgets.*.id' => 'required|exists:dashboard_widgets,id',
            'widgets.*.position_x' => 'required|integer',
            'widgets.*.position_y' => 'required|integer',
        ]);

        foreach ($request->widgets as $widgetData) {
            DashboardWidget::where('id', $widgetData['id'])
                ->where('user_id', auth()->id())
                ->update([
                    'position_x' => $widgetData['position_x'],
                    'position_y' => $widgetData['position_y'],
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleWidget(Request $request)
    {
        $request->validate([
            'widget_type' => 'required|string',
            'is_visible' => 'required|boolean',
        ]);

        $widget = DashboardWidget::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'widget_type' => $request->widget_type,
            ],
            [
                'widget_name' => $this->getWidgetName($request->widget_type),
                'position_x' => 0,
                'position_y' => 0,
                'width' => 4,
                'height' => 4,
            ]
        );

        $widget->update(['is_visible' => $request->is_visible]);

        return response()->json(['success' => true]);
    }

    protected function createDefaultWidgets($user)
    {
        $defaultWidgets = [
            ['type' => 'sales_today', 'name' => 'Sales Today', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
            ['type' => 'sales_month', 'name' => 'Sales This Month', 'x' => 3, 'y' => 0, 'w' => 3, 'h' => 2],
            ['type' => 'tax_collected', 'name' => 'Tax Collected', 'x' => 6, 'y' => 0, 'w' => 3, 'h' => 2],
            ['type' => 'pending_invoices', 'name' => 'Pending Invoices', 'x' => 9, 'y' => 0, 'w' => 3, 'h' => 2],
            ['type' => 'top_customers', 'name' => 'Top Customers', 'x' => 0, 'y' => 2, 'w' => 4, 'h' => 4],
            ['type' => 'top_products', 'name' => 'Top Products', 'x' => 4, 'y' => 2, 'w' => 4, 'h' => 4],
            ['type' => 'returns_summary', 'name' => 'Returns Summary', 'x' => 8, 'y' => 2, 'w' => 4, 'h' => 4],
        ];

        $widgets = [];
        foreach ($defaultWidgets as $index => $widget) {
            $widgets[] = DashboardWidget::create([
                'user_id' => $user->id,
                'widget_type' => $widget['type'],
                'widget_name' => $widget['name'],
                'position_x' => $widget['x'],
                'position_y' => $widget['y'],
                'width' => $widget['w'],
                'height' => $widget['h'],
                'is_visible' => true,
            ]);
        }

        return collect($widgets);
    }

    protected function getWidgetData($widgetType)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        switch ($widgetType) {
            case 'sales_today':
                return [
                    'amount' => SalesInvoice::whereDate('invoice_date', $today)
                        ->whereIn('status', ['posted', 'paid', 'partial'])
                        ->sum('total_amount'),
                    'count' => SalesInvoice::whereDate('invoice_date', $today)
                        ->whereIn('status', ['posted', 'paid', 'partial'])
                        ->count(),
                ];

            case 'sales_month':
                return [
                    'amount' => SalesInvoice::whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['posted', 'paid', 'partial'])
                        ->sum('total_amount'),
                    'count' => SalesInvoice::whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['posted', 'paid', 'partial'])
                        ->count(),
                ];

            case 'tax_collected':
                return [
                    'today' => SalesInvoice::whereDate('invoice_date', $today)
                        ->whereIn('status', ['posted', 'paid', 'partial'])
                        ->sum('tax_amount'),
                    'month' => SalesInvoice::whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['posted', 'paid', 'partial'])
                        ->sum('tax_amount'),
                ];

            case 'pending_invoices':
                return [
                    'count' => SalesInvoice::where('status', 'posted')->count(),
                    'amount' => SalesInvoice::where('status', 'posted')->sum('balance_amount'),
                ];

            case 'top_customers':
                return SalesInvoice::select('customer_id', DB::raw('SUM(total_amount) as total'))
                    ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                    ->whereIn('status', ['posted', 'paid', 'partial'])
                    ->with('customer')
                    ->groupBy('customer_id')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();

            case 'top_products':
                return DB::table('sales_invoice_items')
                    ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(net_amount) as total_amount'))
                    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                    ->whereBetween('sales_invoices.invoice_date', [$startOfMonth, $endOfMonth])
                    ->whereIn('sales_invoices.status', ['posted', 'paid', 'partial'])
                    ->groupBy('product_id')
                    ->orderByDesc('total_qty')
                    ->limit(5)
                    ->get();

            case 'returns_summary':
                return [
                    'month_count' => SalesReturn::whereBetween('return_date', [$startOfMonth, $endOfMonth])
                        ->where('status', 'posted')
                        ->count(),
                    'month_amount' => SalesReturn::whereBetween('return_date', [$startOfMonth, $endOfMonth])
                        ->where('status', 'posted')
                        ->sum('total_amount'),
                ];

            default:
                return null;
        }
    }

    protected function getPendingApprovals($user)
    {
        $approvals = [];

        // Stock transfer requests pending approval
        if ($user->hasPermissionTo('approve transfers')) {
            $approvals['transfer_requests'] = StockTransferRequest::where('status', 'pending')
                ->with(['fromWarehouse', 'toWarehouse', 'requestedBy'])
                ->count();
        }

        return $approvals;
    }

    protected function getWidgetName($type)
    {
        $names = [
            'sales_today' => 'Sales Today',
            'sales_month' => 'Sales This Month',
            'tax_collected' => 'Tax Collected',
            'pending_invoices' => 'Pending Invoices',
            'top_customers' => 'Top Customers',
            'top_products' => 'Top Products',
            'returns_summary' => 'Returns Summary',
            'low_stock' => 'Low Stock Alert',
        ];

        return $names[$type] ?? 'Widget';
    }
}
