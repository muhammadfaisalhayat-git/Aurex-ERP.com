<?php

$path = 'app/Services/AccountingService.php';
$content = file_get_contents($path);

// 1. Fix sub_ledger_type duplicate cases
$content = preg_replace("/'105' => 'employee',\s*\/\/ Accounts Receivable\n/", "'105' => 'employee', // Employee AR / Advances\n", $content);
$content = preg_replace("/'501' => 'employee',\s*\/\/ Accounts Payable\n/", "'501' => 'employee', // Employee AP / Salaries\n", $content);
$content = preg_replace("/'105' => 'employee',\s*\/\/ Employee Advances\/Loans\n/", "", $content);
$content = preg_replace("/'501' => 'employee',\s*\/\/ Salaries\n/", "", $content);

// 2. Add getMappedAccount method
$getMappedAccount = "
    /**
     * Get account by mapping or default code.
     */
    public function getMappedAccount(\$module, \$key)
    {
        \$mapping = \App\Models\AccountMapping::where('module', \$module)
            ->where('key', \$key)
            ->where('is_active', true)
            ->first();

        if (\$mapping) {
            return \$mapping->chartOfAccount;
        }

        // Default mappings if not explicitly defined
        \$defaults = [
            'stock' => [
                'inventory' => '104',
                'ap' => '201',
                'adjustment' => '508',
            ],
        ];

        \$code = \$defaults[\$module][\$key] ?? null;
        return \$code ? \$this->getAccountByCode(\$code) : null;
    }
";

// Insert getMappedAccount after getAccountTypeByCode
$content = preg_replace("/(private function getAccountTypeByCode.*?\}\s+)/s", "$1" . $getMappedAccount . "\n", $content);

// 3. Update stock methods to use getMappedAccount
$content = preg_replace("/\\\$inventoryAcc = \\\$this->getAccountByCode\('104'\);/", "\$inventoryAcc = \$this->getMappedAccount('stock', 'inventory');", $content);
$content = preg_replace("/\\\$apAccount = \\\$this->getAccountByCode\('201'\);/", "\$apAccount = \$this->getMappedAccount('stock', 'ap');", $content);
$content = preg_replace("/\\\$adjustmentAcc = \\\$this->getAccountByCode\('508'\);/", "\$adjustmentAcc = \$this->getMappedAccount('stock', 'adjustment');", $content);

file_put_contents($path, $content);
echo "Refined AccountingService with AccountMapping and deduplicated sub-ledger types.\n";
