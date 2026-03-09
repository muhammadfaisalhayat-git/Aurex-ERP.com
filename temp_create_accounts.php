<?php

use App\Models\ChartOfAccount;

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating accounts for Company 5...\n";

$inv = ChartOfAccount::updateOrCreate(
    ['company_id' => 5, 'code' => '1104'],
    [
        'branch_id' => 5,
        'name_en' => 'Inventory',
        'name_ar' => 'المخزون',
        'type' => 'asset',
        'account_type_id' => 1,
        'is_active' => true,
        'is_posting_allowed' => true
    ]
);

$cogs = ChartOfAccount::updateOrCreate(
    ['company_id' => 5, 'code' => '5001'],
    [
        'branch_id' => 5,
        'name_en' => 'Cost of Goods Sold',
        'name_ar' => 'تكلفة البضاعة المباعة',
        'type' => 'expense',
        'account_type_id' => 5,
        'is_active' => true,
        'is_posting_allowed' => true
    ]
);

echo "Created/Updated: Inventory (ID: {$inv->id}), COGS (ID: {$cogs->id})\n";
