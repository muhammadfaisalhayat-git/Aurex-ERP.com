<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use App\Models\StockLedger;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Employee;
use App\Models\CostCenter;
use App\Models\Activity;
use App\Models\ProductCategory;
use App\Models\LetterOfCredit;
use App\Models\Promoter;
use App\Models\StockSupply;
use App\Models\StockReceiving;
use App\Models\StockTransfer;
use App\Models\StockTransferRequest;
use App\Models\StockIssueOrder;
use App\Models\CompositeAssembly;
use App\Models\Warehouse;
use App\Models\Production\Machine;
use App\Models\Production\WorkCenter;
use App\Models\Production\ProductionOrder;
use App\Models\Logistics\DeliveryVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UniversalReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view accounting reports');
    }

    public function index()
    {
        $entityTypes = [
            'account' => __('messages.account'),
            'customer' => __('messages.customer'),
            'vendor' => __('messages.vendor'),
            'product' => __('messages.product'),
            'category' => __('messages.category'),
            'cost_center' => __('messages.cost_center'),
            'activity' => __('messages.activity'),
            'employee' => __('messages.employee'),
            'warehouse' => __('messages.warehouse'),
            'lc' => __('messages.lc'),
            'promoter' => __('messages.promoter'),
            'stock_supply' => __('messages.stock_supply'),
            'stock_receiving' => __('messages.stock_receiving'),
            'stock_transfer' => __('messages.stock_transfers'),
            'transfer_request' => __('messages.transfer_requests'),
            'issue_order' => __('messages.issue_orders'),
            'composite_assembly' => __('messages.composite_assemblies'),
            'machine' => __('messages.machine'),
            'work_center' => __('messages.work_center'),
            'production_order' => __('messages.production_order'),
            'vehicle' => __('messages.delivery_vehicle'),
        ];

        return view('accounting.reports.universal_statement', compact('entityTypes'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $type = $request->entity_type;
        $id = $request->entity_id;
        $startDate = \Illuminate\Support\Carbon::parse($request->start_date)->startOfDay();
        $endDate = \Illuminate\Support\Carbon::parse($request->end_date)->endOfDay();

        $results = [];
        $openingBalance = 0;
        $entityName = '';

        switch ($type) {
            case 'account':
                $entity = ChartOfAccount::findOrFail($id);
                $entityName = $entity->name;
                $openingBalance = LedgerEntry::where('chart_of_account_id', $id)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('chart_of_account_id', $id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'customer':
                $entity = Customer::findOrFail($id);
                $entityName = $entity->name;
                $openingBalance = LedgerEntry::where('customer_id', $id)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('customer_id', $id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'vendor':
                $entity = Vendor::findOrFail($id);
                $entityName = $entity->name;
                $openingBalance = LedgerEntry::where('vendor_id', $id)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('credit - debit'));
                $results = LedgerEntry::where('vendor_id', $id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'product':
                $entity = Product::findOrFail($id);
                $entityName = $entity->name;
                $openingBalance = StockLedger::where('product_id', $id)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('CASE WHEN movement_type = "in" THEN quantity ELSE -quantity END'));
                $results = StockLedger::where('product_id', $id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'cost_center':
                $entity = CostCenter::findOrFail($id);
                $entityName = __('messages.cost_center') . ": " . $entity->name;
                $openingBalance = LedgerEntry::where('cost_center_no', $entity->code)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('cost_center_no', $entity->code)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'activity':
                $entity = Activity::findOrFail($id);
                $entityName = __('messages.activity') . ": " . $entity->name;
                $openingBalance = LedgerEntry::where('activity_no', $entity->code)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('activity_no', $entity->code)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'employee':
                $entity = Employee::findOrFail($id);
                $entityName = __('messages.employee') . ": " . $entity->first_name_en . ' ' . $entity->last_name_en;
                // Employees might be tracked via customer_id or vendor_id or specific ledger entries
                // Here we assume employee_id field in LedgerEntry if exists, otherwise filter by their name/code as notes
                $openingBalance = LedgerEntry::where('description', 'like', "%$entity->employee_code%")
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('description', 'like', "%$entity->employee_code%")
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'warehouse':
                $entity = Warehouse::findOrFail($id);
                $entityName = $entity->name;
                $openingBalance = StockLedger::where('warehouse_id', $id)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('CASE WHEN movement_type = "in" THEN quantity ELSE -quantity END'));
                $results = StockLedger::where('warehouse_id', $id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'category':
                $entity = ProductCategory::findOrFail($id);
                $entityName = __('messages.category') . ": " . $entity->name;
                $results = StockLedger::whereHas('product', function ($q) use ($id) {
                    $q->where('category_id', $id);
                })
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'lc':
                $entity = LetterOfCredit::findOrFail($id);
                $entityName = __('messages.lc') . ": " . $entity->lc_number . " (" . $entity->bank_name . ")";
                $openingBalance = LedgerEntry::where('lc_no', $entity->lc_number)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('lc_no', $entity->lc_number)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            case 'promoter':
                $entity = Promoter::findOrFail($id);
                $entityName = __('messages.promoter') . ": " . $entity->name;
                $openingBalance = LedgerEntry::where('promoter_code', $entity->code)
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
                $results = LedgerEntry::where('promoter_code', $entity->code)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();
                break;

            // Document-based entities often don't have an "opening balance" in the same way, 
            // but we can show all entries related to a specific document.
            case 'stock_supply':
                $entity = StockSupply::findOrFail($id);
                $entityName = __('messages.stock_supply') . ": " . $entity->document_number;
                $results = StockLedger::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;
            case 'stock_receiving':
                $entity = StockReceiving::findOrFail($id);
                $entityName = __('messages.stock_receiving') . ": " . $entity->document_number;
                $results = StockLedger::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;
            case 'stock_transfer':
                $entity = StockTransfer::findOrFail($id);
                $entityName = __('messages.stock_transfers') . ": " . $entity->document_number;
                $results = StockLedger::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;
            case 'transfer_request':
                $entity = StockTransferRequest::findOrFail($id);
                $entityName = __('messages.transfer_requests') . ": " . $entity->document_number;
                $results = StockLedger::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;
            case 'issue_order':
                $entity = StockIssueOrder::findOrFail($id);
                $entityName = __('messages.issue_orders') . ": " . $entity->document_number;
                $results = StockLedger::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;
            case 'composite_assembly':
                $entity = CompositeAssembly::findOrFail($id);
                $entityName = __('messages.composite_assemblies') . ": " . $entity->document_number;
                $results = StockLedger::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;

            case 'machine':
                $entity = Machine::findOrFail($id);
                $entityName = __('messages.machine') . ": " . $entity->name;
                $results = LedgerEntry::where('description', 'like', "%$entity->name%")
                    ->orWhere('description', 'like', "%$entity->code%")
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->get();
                break;
            case 'work_center':
                $entity = WorkCenter::findOrFail($id);
                $entityName = __('messages.work_center') . ": " . $entity->name;
                $results = LedgerEntry::where('description', 'like', "%$entity->name%")
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->get();
                break;
            case 'production_order':
                $entity = ProductionOrder::findOrFail($id);
                $entityName = __('messages.production_order') . ": " . $entity->order_number;
                $results = LedgerEntry::where('reference_type', get_class($entity))
                    ->where('reference_id', $id)
                    ->get();
                break;
            case 'vehicle':
                $entity = DeliveryVehicle::findOrFail($id);
                $entityName = __('messages.delivery_vehicle') . ": " . $entity->plate_number;
                $results = LedgerEntry::where('description', 'like', "%$entity->plate_number%")
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->get();
                break;
        }

        return view('accounting.reports.universal_statement_result', compact(
            'results',
            'openingBalance',
            'entityName',
            'type',
            'startDate',
            'endDate'
        ));
    }

    public function searchEntities(Request $request)
    {
        $type = $request->type;
        $q = $request->q;

        switch ($type) {
            case 'account':
                $results = ChartOfAccount::posting()
                    ->where(function ($query) use ($q) {
                        $query->where('name_en', 'like', "%$q%")
                            ->orWhere('name_ar', 'like', "%$q%")
                            ->orWhere('code', 'like', "%$q%");
                    })->limit(20)->get();
                break;
            case 'customer':
                $results = Customer::where(function ($query) use ($q) {
                    $query->where('name_en', 'like', "%$q%")
                        ->orWhere('name_ar', 'like', "%$q%")
                        ->orWhere('code', 'like', "%$q%");
                })->limit(20)->get();
                break;
            case 'vendor':
                $results = Vendor::where(function ($query) use ($q) {
                    $query->where('name_en', 'like', "%$q%")
                        ->orWhere('name_ar', 'like', "%$q%")
                        ->orWhere('code', 'like', "%$q%");
                })->limit(20)->get();
                break;
            case 'product':
                $results = Product::where(function ($query) use ($q) {
                    $query->where('name_en', 'like', "%$q%")
                        ->orWhere('name_ar', 'like', "%$q%")
                        ->orWhere('code', 'like', "%$q%");
                })->limit(20)->get();
                break;
            case 'employee':
                $results = Employee::where(function ($query) use ($q) {
                    $query->where('first_name_en', 'like', "%$q%")
                        ->orWhere('last_name_en', 'like', "%$q%")
                        ->orWhere('employee_code', 'like', "%$q%");
                })->limit(20)->get();
                break;
            case 'warehouse':
                $results = \App\Models\Warehouse::where('name', 'like', "%$q%")
                    ->orWhere('location', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'cost_center':
                $results = CostCenter::where(function ($query) use ($q) {
                    $query->where('name_en', 'like', "%$q%")
                        ->orWhere('name_ar', 'like', "%$q%")
                        ->orWhere('code', 'like', "%$q%");
                })->limit(20)->get();
                break;
            case 'activity':
                $results = Activity::where(function ($query) use ($q) {
                    $query->where('name_en', 'like', "%$q%")
                        ->orWhere('name_ar', 'like', "%$q%")
                        ->orWhere('code', 'like', "%$q%");
                })->limit(20)->get();
                break;
            case 'category':
                $results = ProductCategory::where('name', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'lc':
                $results = LetterOfCredit::where('lc_number', 'like', "%$q%")
                    ->orWhere('bank_name', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'promoter':
                $results = Promoter::where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'stock_supply':
                $results = StockSupply::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'stock_receiving':
                $results = StockReceiving::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'stock_transfer':
                $results = StockTransfer::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'transfer_request':
                $results = StockTransferRequest::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'issue_order':
                $results = StockIssueOrder::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'composite_assembly':
                $results = CompositeAssembly::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'machine':
                $results = Machine::where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'work_center':
                $results = WorkCenter::where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'production_order':
                $results = ProductionOrder::where('document_number', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            case 'vehicle':
                $results = DeliveryVehicle::where('plate_number', 'like', "%$q%")
                    ->orWhere('vehicle_name', 'like', "%$q%")
                    ->limit(20)->get();
                break;
            default:
                return response()->json([]);
        }

        $locale = app()->getLocale();
        return response()->json($results->map(function ($item) use ($locale) {
            $name = $item->name ?? $item->code ?? $item->id;
            if (isset($item->name_en) || isset($item->name_ar)) {
                $name = $locale === 'ar' ? ($item->name_ar ?? $item->name_en) : ($item->name_en ?? $item->name_ar);
            } elseif (isset($item->first_name_en)) {
                $name = $item->first_name_en . ' ' . $item->last_name_en;
            } elseif (isset($item->lc_number)) {
                $name = $item->lc_number . " (" . $item->bank_name . ")";
            } elseif (isset($item->document_number)) {
                $name = $item->document_number;
            } elseif (isset($item->order_number)) {
                $name = $item->order_number;
            } elseif (isset($item->plate_number)) {
                $name = $item->plate_number . " - " . ($item->vehicle_name ?? '');
            }

            return [
                'id' => $item->id,
                'text' => ($item->code ? '[' . $item->code . '] ' : '') . $name
            ];
        }));
    }
}
