<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\MeasurementUnit;
use App\Models\ProductBom;
use App\Models\StockLedger;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view inventory')->only(['index', 'show', 'bom']);
        $this->middleware('can:create inventory')->only(['create', 'store', 'createBom', 'storeBom']);
        $this->middleware('can:edit inventory')->only(['edit', 'update', 'editBom', 'updateBom']);
        $this->middleware('can:delete inventory')->only(['destroy']);
    }
    public function index()
    {
        $products = Product::with('category')
            ->withSum('stockBalances', 'available_quantity')
            ->paginate(20);
        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        $warehouses = Warehouse::active()->get();
        $movements = collect();
        $fromDate = Carbon::now()->startOfYear()->toDateString();
        $toDate = Carbon::now()->endOfDay()->toDateString();
        $warehouseId = null;
        $movementType = null;
        $measurementUnits = MeasurementUnit::active()->orderBy('name')->get();
        $suggestedCode = $this->generateProductCode();

        return view('inventory.products.create', compact(
            'categories',
            'warehouses',
            'movements',
            'fromDate',
            'toDate',
            'warehouseId',
            'movementType',
            'measurementUnits',
            'suggestedCode'
        ));
    }

    private function generateProductCode(): string
    {
        $year = date('Y');
        $prefix = $year . '-';

        // Find all product codes for this year, then pick the max sequential number
        $products = Product::where('code', 'like', $prefix . '%')->get(['code']);
        $maxNum = 0;
        foreach ($products as $p) {
            $suffix = substr($p->code, strlen($prefix));
            if (is_numeric($suffix)) {
                $maxNum = max($maxNum, (int) $suffix);
            }
        }

        return $prefix . ($maxNum + 1);
    }

    public function show(Product $product, Request $request)
    {
        $product->load(['category', 'images', 'bomComponents.component', 'units.measurementUnit']);
        $warehouses = Warehouse::active()->get();

        $fromDate = $request->get('from_date', Carbon::now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', Carbon::now()->endOfDay()->toDateString());
        $warehouseId = $request->get('warehouse_id');
        $movementType = $request->get('movement_type');

        $movements = StockLedger::where('product_id', $product->id)
            ->whereBetween('transaction_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($movementType, fn($q) => $q->where('movement_type', $movementType))
            ->with(['warehouse', 'creator'])
            ->orderBy('transaction_date', 'asc')
            ->get();

        return view('inventory.products.show', compact(
            'product',
            'warehouses',
            'movements',
            'fromDate',
            'toDate',
            'warehouseId',
            'movementType'
        ));
    }

    public function edit(Product $product, Request $request)
    {
        $product->load('units');
        $categories = ProductCategory::all();
        $warehouses = Warehouse::active()->get();
        $measurementUnits = MeasurementUnit::active()->orderBy('name')->get();

        $fromDate = $request->get('from_date', Carbon::now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', Carbon::now()->endOfDay()->toDateString());
        $warehouseId = $request->get('warehouse_id');
        $movementType = $request->get('movement_type');

        $movements = StockLedger::where('product_id', $product->id)
            ->whereBetween('transaction_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($movementType, fn($q) => $q->where('movement_type', $movementType))
            ->with(['warehouse', 'creator'])
            ->orderBy('transaction_date', 'asc')
            ->get();

        return view('inventory.products.edit', compact(
            'product',
            'categories',
            'warehouses',
            'movements',
            'fromDate',
            'toDate',
            'warehouseId',
            'movementType',
            'measurementUnits'
        ));
    }

    public function bom(Product $product)
    {
        $product->load('bomComponents.component', 'bomComponents.measurementUnit');
        $measurementUnits = MeasurementUnit::active()->orderBy('name')->get();
        return view('inventory.products.bom', compact('product', 'measurementUnits'));
    }

    public function destroyBom(Product $product, ProductBom $bom)
    {
        if ($bom->product_id !== $product->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $bom->delete();
        return redirect()->back()->with('success', __('messages.component_deleted'));
    }

    public function updateBom(Request $request, Product $product)
    {
        $validated = $request->validate([
            'component_id' => 'required|exists:products,id',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
            'quantity' => 'required|numeric|min:0.0001',
            'waste_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        $product->bomComponents()->updateOrCreate(
            ['component_id' => $validated['component_id']],
            [
                'measurement_unit_id' => $validated['measurement_unit_id'],
                'quantity' => $validated['quantity'],
                'waste_percentage' => $validated['waste_percentage'] ?? 0,
                'notes' => $validated['notes'] ?? null
            ]
        );

        return redirect()->back()->with('success', __('messages.bom_updated'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:simple,composite,service',
            'barcode' => 'nullable|string|max:255',
            'gtin' => 'nullable|string|max:255',
            'hsn_code' => 'nullable|string|max:255',
            'manufacturer_code' => 'nullable|string|max:255',
            'ref_code' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'primary_cost' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'unit_of_measure' => 'nullable|string|max:50',
            'default_unit' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'size_dimension' => 'nullable|numeric|min:0',
            'decimals_count' => 'nullable|integer|min:0|max:5',
            'reorder_level' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'purchase_inv_no' => 'nullable|string|max:100',
            'return_period' => 'nullable|integer|min:0',
            'item_activity' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:255',
            'measure' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'season' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'manufacturer_company' => 'nullable|string|max:255',
            'country_of_origin' => 'nullable|string|max:255',
            'items_storage' => 'nullable|string|max:255',
            'weights_base' => 'nullable|string|max:255',
            'inactivation_date' => 'nullable|date',
            'deactivation_reason' => 'nullable|string',
            'return_period_before_expiry' => 'nullable|integer|min:0',
            'no_of_printing_times' => 'nullable|integer|min:0',
            'no_of_modifications' => 'nullable|integer|min:0',
            'item_units' => 'nullable|array',
            'item_units.*.measurement_unit_id' => 'required|exists:measurement_units,id',
            'item_units.*.package' => 'required|numeric|min:0.0001',
            'item_units.*.price' => 'nullable|numeric|min:0',
            'item_units.*.barcode' => 'nullable|string|max:255',
            'item_units.*.description' => 'nullable|string|max:255',
            'item_units.*.foreign_description' => 'nullable|string|max:255',
        ]);

        $booleanFlags = [
            'is_active',
            'is_sellable',
            'is_purchasable',
            'is_weighted',
            'is_reserved',
            'is_not_for_sale',
            'is_controlled',
            'allow_fractions',
            'sold_in_cash',
            'is_asset',
            'use_partition',
            'is_compound',
            'is_component',
            'is_non_returnable',
            'use_expiry_date',
            'is_requirement',
            'show_in_vss',
            'use_custodians',
            'use_in_crm',
            'has_alternatives',
            'item_code_as_serial',
            'show_in_css'
        ];

        foreach ($booleanFlags as $flag) {
            $validated[$flag] = $request->has($flag);
        }

        $product = Product::create($validated);

        if ($request->has('item_units')) {
            foreach ($request->item_units as $unitData) {
                $product->units()->create([
                    'measurement_unit_id' => $unitData['measurement_unit_id'],
                    'package' => $unitData['package'] ?? 1,
                    'price' => $unitData['price'] ?? 0,
                    'barcode' => $unitData['barcode'] ?? null,
                    'description' => $unitData['description'] ?? null,
                    'foreign_description' => $unitData['foreign_description'] ?? null,
                    'is_purchase_unit' => isset($unitData['is_purchase_unit']),
                    'is_transfer_unit' => isset($unitData['is_transfer_unit']),
                    'is_stocktaking_unit' => isset($unitData['is_stocktaking_unit']),
                    'is_not_for_sale' => isset($unitData['is_not_for_sale']),
                    'is_inactive' => isset($unitData['is_inactive']),
                    'is_production_unit' => isset($unitData['is_production_unit']),
                    'is_store_unit' => isset($unitData['is_store_unit']),
                    'is_customer_self_service' => isset($unitData['is_customer_self_service']),
                    'excluded_from_discount' => isset($unitData['excluded_from_discount']),
                ]);
            }
        }

        return redirect()->route('inventory.products.index')
            ->with('success', __('messages.product_created'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:simple,composite,service',
            'barcode' => 'nullable|string|max:255',
            'gtin' => 'nullable|string|max:255',
            'hsn_code' => 'nullable|string|max:255',
            'manufacturer_code' => 'nullable|string|max:255',
            'ref_code' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'primary_cost' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'unit_of_measure' => 'nullable|string|max:50',
            'default_unit' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'size_dimension' => 'nullable|numeric|min:0',
            'decimals_count' => 'nullable|integer|min:0|max:5',
            'reorder_level' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'purchase_inv_no' => 'nullable|string|max:100',
            'return_period' => 'nullable|integer|min:0',
            'item_activity' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:255',
            'measure' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'season' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'manufacturer_company' => 'nullable|string|max:255',
            'country_of_origin' => 'nullable|string|max:255',
            'items_storage' => 'nullable|string|max:255',
            'weights_base' => 'nullable|string|max:255',
            'inactivation_date' => 'nullable|date',
            'deactivation_reason' => 'nullable|string',
            'return_period_before_expiry' => 'nullable|integer|min:0',
            'no_of_printing_times' => 'nullable|integer|min:0',
            'no_of_modifications' => 'nullable|integer|min:0',
            'item_units' => 'nullable|array',
            'item_units.*.measurement_unit_id' => 'required|exists:measurement_units,id',
            'item_units.*.package' => 'required|numeric|min:0.0001',
            'item_units.*.price' => 'nullable|numeric|min:0',
            'item_units.*.barcode' => 'nullable|string|max:255',
            'item_units.*.description' => 'nullable|string|max:255',
            'item_units.*.foreign_description' => 'nullable|string|max:255',
        ]);

        $booleanFlags = [
            'is_active',
            'is_sellable',
            'is_purchasable',
            'is_weighted',
            'is_reserved',
            'is_not_for_sale',
            'is_controlled',
            'allow_fractions',
            'sold_in_cash',
            'is_asset',
            'use_partition',
            'is_compound',
            'is_component',
            'is_non_returnable',
            'use_expiry_date',
            'is_requirement',
            'show_in_vss',
            'use_custodians',
            'use_in_crm',
            'has_alternatives',
            'item_code_as_serial',
            'show_in_css'
        ];

        foreach ($booleanFlags as $flag) {
            $validated[$flag] = $request->has($flag);
        }

        $product->update($validated);

        // Sync item units: simple delete all and recreate
        $product->units()->delete();
        if ($request->has('item_units')) {
            foreach ($request->item_units as $unitData) {
                $product->units()->create([
                    'measurement_unit_id' => $unitData['measurement_unit_id'],
                    'package' => $unitData['package'] ?? 1,
                    'price' => $unitData['price'] ?? 0,
                    'barcode' => $unitData['barcode'] ?? null,
                    'description' => $unitData['description'] ?? null,
                    'foreign_description' => $unitData['foreign_description'] ?? null,
                    'is_purchase_unit' => isset($unitData['is_purchase_unit']),
                    'is_transfer_unit' => isset($unitData['is_transfer_unit']),
                    'is_stocktaking_unit' => isset($unitData['is_stocktaking_unit']),
                    'is_not_for_sale' => isset($unitData['is_not_for_sale']),
                    'is_inactive' => isset($unitData['is_inactive']),
                    'is_production_unit' => isset($unitData['is_production_unit']),
                    'is_store_unit' => isset($unitData['is_store_unit']),
                    'is_customer_self_service' => isset($unitData['is_customer_self_service']),
                    'excluded_from_discount' => isset($unitData['excluded_from_discount']),
                ]);
            }
        }

        return redirect()->route('inventory.products.index')
            ->with('success', __('messages.product_updated'));
    }

    public function destroy(Product $product)
    {
        // Check for dependencies if necessary, but SoftDeletes is used
        $product->delete();

        return redirect()->route('inventory.products.index')
            ->with('success', __('messages.product_deleted'));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $warehouseId = $request->get('warehouse_id');
        $branchId = $request->get('branch_id');

        $products = Product::where('is_sellable', true)
            ->with(['units.measurementUnit'])
            ->where(function ($query) use ($search) {
                $query->where('name_en', 'like', "%$search%")
                    ->orWhere('name_ar', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('id', $search);
            })
            ->limit(10)
            ->get();

        $results = $products->map(function ($product) use ($warehouseId, $branchId) {
            $available = 0;
            if ($warehouseId) {
                $balance = $product->stockBalances()->where('warehouse_id', $warehouseId)->first();
                $available = $balance ? $balance->available_quantity : 0;
            } elseif ($branchId) {
                $available = $product->stockBalances()->whereHas('warehouse', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })->sum('available_quantity');
            } else {
                $available = $product->stockBalances()->sum('available_quantity');
            }

            return [
                'id' => $product->id,
                'code' => $product->code,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar,
                'sale_price' => $product->sale_price,
                'cost_price' => $product->cost_price,
                'available_quantity' => $available,
                'tax' => $product->tax_rate,
                'decimals_count' => $product->decimals_count,
                'units' => $product->units->map(function ($unit) {
                    return [
                        'measurement_unit_id' => $unit->measurement_unit_id,
                        'name' => $unit->measurementUnit->name ?? '',
                        'package' => $unit->package,
                        'price' => $unit->price
                    ];
                })->toArray()
            ];
        });

        return response()->json($results);
    }

    /**
     * Get stock balance for a product in a specific warehouse via AJAX.
     */
    public function ajaxStock(Request $request)
    {
        $productId = $request->get('product_id');
        $warehouseId = $request->get('warehouse_id');
        $branchId = $request->get('branch_id');

        if (!$productId) {
            return response()->json(['error' => 'Product ID is required'], 400);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $available = 0;
        if ($warehouseId) {
            $balance = $product->stockBalances()->where('warehouse_id', $warehouseId)->first();
            $available = $balance ? $balance->available_quantity : 0;
        } elseif ($branchId) {
            $available = $product->stockBalances()->whereHas('warehouse', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->sum('available_quantity');
        } else {
            $available = $product->stockBalances()->sum('available_quantity');
        }

        return response()->json([
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'branch_id' => $branchId,
            'available_quantity' => $available,
            'unit' => $product->unit_of_measure
        ]);
    }
}
