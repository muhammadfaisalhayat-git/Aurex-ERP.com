<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\Company;

class ErpAccountSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first();
        if (!$company)
            return;

        $accounts = [
            [
                'company_id' => $company->id,
                'code' => '104-FG',
                'name_en' => 'Finished Goods Inventory',
                'name_ar' => 'مخزون المنتجات التامة',
                'account_type_id' => 1,
                'type' => 'asset',
                'is_active' => true,
                'is_posting_allowed' => true,
            ],
            [
                'company_id' => $company->id,
                'code' => '104-WIP',
                'name_en' => 'Work In Progress (WIP)',
                'name_ar' => 'إنتاج تحت التشغيل',
                'account_type_id' => 1,
                'type' => 'asset',
                'is_active' => true,
                'is_posting_allowed' => true,
            ],
            [
                'company_id' => $company->id,
                'code' => '506',
                'name_en' => 'Fuel & Transport Expense',
                'name_ar' => 'مصروفات الوقود والنقل',
                'account_type_id' => 5,
                'type' => 'expense',
                'is_active' => true,
                'is_posting_allowed' => true,
            ],
            [
                'company_id' => $company->id,
                'code' => '101',
                'name_en' => 'Cash/Bank clearing',
                'name_ar' => 'تسوية النقدية/البنك',
                'account_type_id' => 1,
                'type' => 'asset',
                'is_active' => true,
                'is_posting_allowed' => true,
            ],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(
            ['code' => $account['code'], 'company_id' => $account['company_id']],
                $account
            );
        }
    }
}
