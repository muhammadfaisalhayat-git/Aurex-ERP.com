<?php

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockBalance;
use App\Models\JournalVoucher;
use App\Models\LedgerEntry;
use App\Services\StockManagementService;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configuration for Company 5, Warehouse 7
$targetQty = 10;
$companyId = 5;
$branchId = 10; // Bin Awf Agricultural - Tayma
$warehouseId = 7; // Bin Awf Agricultural Warehouse- Tayma
$inventoryAccountId = 53; // Newly created Inventory for Co 5
$adjustmentAccountId = 54; // Newly created COGS for Co 5
$userId = 1;

echo "Starting stock maintenance for Company ID: {$companyId}, Warehouse ID: {$warehouseId}...\n";

$stockService = app(StockManagementService::class);
$accountingService = app(AccountingService::class);

// Fetch all products
$products = Product::withoutGlobalScopes()->get();

foreach ($products as $product) {
    // Get current balance explicitly
    $balance = StockBalance::withoutGlobalScopes()
        ->where('company_id', $companyId)
        ->where('product_id', $product->id)
        ->where('warehouse_id', $warehouseId)
        ->first();

    $currentQty = $balance ? (float) $balance->quantity : 0;

    if ($currentQty == $targetQty) {
        echo "Product {$product->code}: Already at {$targetQty}\n";
        continue;
    }

    $adjustmentQty = $targetQty - $currentQty;
    $movementType = $adjustmentQty > 0 ? 'in' : 'out';
    $absoluteQty = abs($adjustmentQty);

    echo "Product {$product->code}: Adjusting from {$currentQty} to {$targetQty} (Movement: {$movementType}, Qty: {$absoluteQty})\n";

    DB::transaction(function () use ($stockService, $accountingService, $product, $warehouseId, $movementType, $absoluteQty, $companyId, $branchId, $adjustmentAccountId, $inventoryAccountId, $userId) {
        // 1. Record Movement
        $ledger = $stockService->recordMovement([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'movement_type' => $movementType,
            'quantity' => $absoluteQty,
            'unit_cost' => $product->cost_price ?? 0,
            'reference_type' => 'manual_maintenance',
            'reference_id' => 0,
            'reference_number' => 'MAINT-' . date('Ymd'),
            'notes' => 'Bulk maintenance to set stock to 10 units.',
            'created_by' => $userId
        ]);

        // 2. Post to Accounting
        $totalAdjustmentValue = $absoluteQty * ($product->cost_price ?? 0);

        if ($totalAdjustmentValue > 0) {
            $jv = JournalVoucher::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'voucher_number' => 'JV-MAINT-' . $product->id . '-' . date('His'),
                'voucher_date' => now(),
                'status' => 'posted',
                'description' => 'Stock adjustment for ' . $product->name . ' to 10 units',
                'total_debit' => $totalAdjustmentValue,
                'total_credit' => $totalAdjustmentValue,
                'created_by' => $userId
            ]);

            $commonLedgerData = [
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'journal_voucher_id' => $jv->id,
                'transaction_date' => now(),
                'reference_type' => 'manual_maintenance',
                'reference_id' => 0,
                'reference_number' => 'MAINT-' . date('Ymd'),
                'created_by' => $userId
            ];

            if ($movementType === 'in') {
                // Debit Inventory, Credit Adjustment
                $accountingService->createLedgerEntry(array_merge($commonLedgerData, [
                    'chart_of_account_id' => $inventoryAccountId,
                    'debit' => $totalAdjustmentValue,
                    'credit' => 0,
                    'description' => 'Stock Increase maintenance'
                ]));
                $accountingService->createLedgerEntry(array_merge($commonLedgerData, [
                    'chart_of_account_id' => $adjustmentAccountId,
                    'debit' => 0,
                    'credit' => $totalAdjustmentValue,
                    'description' => 'Stock Increase maintenance offset'
                ]));
            } else {
                // Debit Adjustment, Credit Inventory
                $accountingService->createLedgerEntry(array_merge($commonLedgerData, [
                    'chart_of_account_id' => $adjustmentAccountId,
                    'debit' => $totalAdjustmentValue,
                    'credit' => 0,
                    'description' => 'Stock Decrease maintenance'
                ]));
                $accountingService->createLedgerEntry(array_merge($commonLedgerData, [
                    'chart_of_account_id' => $inventoryAccountId,
                    'debit' => 0,
                    'credit' => $totalAdjustmentValue,
                    'description' => 'Stock Decrease maintenance offset'
                ]));
            }
        }
    });
}

echo "Maintenance complete.\n";
