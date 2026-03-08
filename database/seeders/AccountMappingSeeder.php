<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountMapping;
use App\Models\ChartOfAccount;
use App\Models\Company;

class AccountMappingSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        $mappings = [
            ['module' => 'stock', 'key' => 'inventory', 'code' => '104'],
            ['module' => 'stock', 'key' => 'adjustment', 'code' => '508'],
            ['module' => 'sales', 'key' => 'receivable', 'code' => '103'],
            ['module' => 'sales', 'key' => 'revenue', 'code' => '401'],
            ['module' => 'purchase', 'key' => 'payable', 'code' => '201'],
            ['module' => 'purchase', 'key' => 'clearing', 'code' => '101'],
            ['module' => 'tax', 'key' => 'payable', 'code' => '202'],
            ['module' => 'transport', 'key' => 'expense', 'code' => '506'],
            ['module' => 'transport', 'key' => 'clearing', 'code' => '101'],
        ];

        foreach ($mappings as $map) {
            $account = ChartOfAccount::where('code', $map['code'])
                ->where('company_id', $company->id)
                ->first();

            if ($account) {
                AccountMapping::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'module' => $map['module'],
                        'key' => $map['key'],
                    ],
                    [
                        'chart_of_account_id' => $account->id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
