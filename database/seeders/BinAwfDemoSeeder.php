<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\StockSupply;
use App\Models\StockSupplyItem;
use App\Models\DocumentNumber;
use App\Models\User;
use App\Services\StockManagementService;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class BinAwfDemoSeeder extends Seeder
{
    public function run()
    {
        $companyId = 5;
        $branchId = 10;
        $admin = User::where('company_id', $companyId)->first() ?? User::first();
        auth()->login($admin);
        session(['active_company_id' => $companyId, 'active_branch_id' => $branchId]);

        DB::transaction(function () use ($companyId, $branchId, $admin) {
            // 1. Warehouse
            $warehouse = Warehouse::withoutGlobalScope('tenant')->updateOrCreate(
                ['company_id' => $companyId, 'code' => 'WH001'],
                ['branch_id' => $branchId, 'name_en' => 'Main Warehouse', 'name_ar' => 'المستودع الرئيسي', 'is_active' => true]
            );

            // 2. Vendor
            $vendor = Vendor::withoutGlobalScope('tenant')->updateOrCreate(
                ['company_id' => $companyId, 'name_en' => 'Agricultural Supplies Co.'],
                ['code' => 'VND-DEMO-01', 'name_ar' => 'شركة التوريدات الزراعية', 'status' => 'active', 'address' => 'Riyadh Road, Tayma', 'branch_id' => $branchId]
            );

            // 3. Products
            $products = [
                ['code' => 'P-001', 'name_en' => 'Premium Fertilizer', 'name_ar' => 'سماد ممتاز', 'category_id' => 7, 'cost_price' => 150, 'sale_price' => 200],
                ['code' => 'P-002', 'name_en' => 'Organic Seeds', 'name_ar' => 'بذور عضوية', 'category_id' => 7, 'cost_price' => 45, 'sale_price' => 75],
                ['code' => 'P-003', 'name_en' => 'Irrigation Pipe 20m', 'name_ar' => 'أنبوب ري 20 متر', 'category_id' => 4, 'cost_price' => 120, 'sale_price' => 180],
            ];

            $createdProducts = [];
            foreach ($products as $pData) {
                $createdProducts[] = Product::withoutGlobalScope('tenant')->updateOrCreate(
                    ['company_id' => $companyId, 'code' => $pData['code']],
                    array_merge($pData, [
                        'branch_id' => $branchId,
                        'type' => 'purchasable',
                        'is_active' => true,
                        'is_purchasable' => true,
                        'is_sellable' => true,
                        'unit_of_measure' => 'Unit',
                    ])
                );
            }

            // 4. Initial Stock Supply
            $supply = StockSupply::withoutGlobalScope('tenant')->where('document_number', 'SS-DEMO-001')->first();

            if (!$supply) {
                $supply = StockSupply::create([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'document_number' => 'SS-DEMO-001',
                    'supply_date' => now(),
                    'warehouse_id' => $warehouse->id,
                    'vendor_id' => $vendor->id,
                    'status' => 'draft',
                    'total_amount' => 0,
                    'created_by' => $admin->id,
                ]);

                $totalAmount = 0;
                foreach ($createdProducts as $product) {
                    $qty = rand(10, 50);
                    $cost = $product->cost_price;
                    StockSupplyItem::create([
                        'stock_supply_id' => $supply->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_cost' => $cost,
                        'total_cost' => $qty * $cost,
                    ]);
                    $totalAmount += ($qty * $cost);
                }

                $supply->update(['total_amount' => $totalAmount]);

                // 5. Post the Supply (using services)
                $stockService = app(StockManagementService::class);
                $accountingService = app(AccountingService::class);

                foreach ($supply->items as $item) {
                    $stockService->recordMovement([
                        'company_id' => $companyId,
                        'branch_id' => $branchId,
                        'product_id' => $item->product_id,
                        'warehouse_id' => $supply->warehouse_id,
                        'transaction_date' => $supply->supply_date,
                        'reference_type' => 'stock_supply',
                        'reference_id' => $supply->id,
                        'reference_number' => $supply->document_number,
                        'movement_type' => 'in',
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->unit_cost,
                        'created_by' => $admin->id,
                    ]);
                }

                // Mock session for accounting service if it uses session
                session(['active_company_id' => $companyId, 'active_branch_id' => $branchId]);

                if ($accountingService->postStockSupply($supply)) {
                    $supply->update([
                        'status' => 'posted',
                        'posted_at' => now(),
                        'posted_by' => $admin->id,
                    ]);
                }
            }
        });
    }
}
