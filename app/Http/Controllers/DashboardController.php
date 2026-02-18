<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesReturn;
use App\Models\StockTransferRequest;
use App\Models\DashboardWidget;
use App\Models\Employee;
use App\Models\Department;
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

        // If no widgets configured, or HR Manager has no HR widgets, create defaults
        $hasHrWidget = $widgets->whereIn('widget_type', ['total_employees', 'payroll_cost', 'active_departments'])->count() > 0;

        if ($widgets->isEmpty() || ($user->hasRole('HR Manager') && !$hasHrWidget)) {
            // If we are forcing defaults for HR Manager, clear old ones first
            if ($user->hasRole('HR Manager') && !$hasHrWidget && !$widgets->isEmpty()) {
                DashboardWidget::where('user_id', $user->id)->delete();
            }
            $widgets = $this->createDefaultWidgets($user);
        }

        $widgetData = [];
        foreach ($widgets as $widget) {
            $widgetData[$widget->widget_type] = $this->getWidgetData($widget->widget_type);
        }

        // Get recent audit logs for Super Admin or Company Admin
        $recentActivities = null;
        if ($user->hasRole(['Super Admin', 'Company Admin'])) {
            $recentActivities = AuditLog::with('user')
                ->where('company_id', session('active_company_id'))
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

    public function resetWidgets()
    {
        DashboardWidget::where('user_id', auth()->id())->delete();
        return redirect()->route('dashboard')->with('success', __('messages.dashboard_reset_success'));
    }

    protected function createDefaultWidgets($user)
    {
        $defaultWidgets = [];

        if ($user->hasRole('HR Manager')) {
            $defaultWidgets = [
                ['type' => 'total_employees', 'name' => 'Total Employees', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'new_hires_month', 'name' => 'New Hires (Month)', 'x' => 3, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'active_departments', 'name' => 'Active Departments', 'x' => 6, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'payroll_cost', 'name' => 'Payroll Cost', 'x' => 9, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'employee_distribution', 'name' => 'Employee Distribution', 'x' => 0, 'y' => 2, 'w' => 6, 'h' => 4],
            ];
        } elseif ($user->hasRole(['Sales Manager', 'Accountant'])) {
            $defaultWidgets = [
                ['type' => 'sales_today', 'name' => 'Sales Today', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'sales_month', 'name' => 'Sales This Month', 'x' => 3, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'tax_collected', 'name' => 'Tax Collected', 'x' => 6, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'pending_invoices', 'name' => 'Pending Invoices', 'x' => 9, 'y' => 0, 'w' => 3, 'h' => 2],
            ];
        } else {
            // Default Mix for Super Admin or others
            $defaultWidgets = [
                ['type' => 'sales_today', 'name' => 'Sales Today', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'total_employees', 'name' => 'Total Employees', 'x' => 3, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'sales_month', 'name' => 'Sales This Month', 'x' => 6, 'y' => 0, 'w' => 3, 'h' => 2],
                ['type' => 'pending_invoices', 'name' => 'Pending Invoices', 'x' => 9, 'y' => 0, 'w' => 3, 'h' => 2],
            ];
        }

        // Add some table widgets if relevant
        if (!$user->hasRole('HR Manager')) {
            $defaultWidgets[] = ['type' => 'top_customers', 'name' => 'Top Customers', 'x' => 0, 'y' => 2, 'w' => 4, 'h' => 4];
            $defaultWidgets[] = ['type' => 'top_products', 'name' => 'Top Products', 'x' => 4, 'y' => 2, 'w' => 4, 'h' => 4];
            $defaultWidgets[] = ['type' => 'returns_summary', 'name' => 'Returns Summary', 'x' => 8, 'y' => 2, 'w' => 4, 'h' => 4];
        }

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

            // HR Widgets
            case 'total_employees':
                return [
                    'count' => Employee::where('status', 'active')->count(),
                ];

            case 'new_hires_month':
                return [
                    'count' => Employee::whereBetween('joining_date', [$startOfMonth, $endOfMonth])->count(),
                ];

            case 'active_departments':
                return [
                    'count' => Department::where('is_active', true)->count(),
                ];

            case 'payroll_cost':
                $employees = Employee::where('status', 'active')->get();
                $total = $employees->sum(function ($emp) {
                    return $emp->basic_salary +
                        $emp->house_rent_allowance +
                        $emp->conveyance_allowance +
                        $emp->dearness_allowance +
                        $emp->overtime_allowance +
                        $emp->other_allowance;
                });
                return [
                    'amount' => $total,
                ];

            case 'employee_distribution':
                return Employee::select('department_id', DB::raw('count(*) as total'))
                    ->where('status', 'active')
                    ->with('department')
                    ->groupBy('department_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'label' => $item->department ? $item->department->name : 'Unassigned',
                            'total' => $item->total
                        ];
                    });

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
            'total_employees' => 'Total Employees',
            'new_hires_month' => 'New Hires (Month)',
            'active_departments' => 'Active Departments',
            'payroll_cost' => 'Payroll Cost',
            'employee_distribution' => 'Employee Distribution',
        ];

        return $names[$type] ?? 'Widget';
    }
}
