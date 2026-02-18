<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class AccountingSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name_en' => 'Fixed Assets', 'name_ar' => 'الأصول الثابتة', 'type' => 'asset', 'is_active' => true, 'is_posting_allowed' => false],
            ['code' => '1001', 'name_en' => 'Machinery & Equipment', 'name_ar' => 'آلات ومعدات', 'type' => 'asset', 'parent_code' => '1000', 'is_active' => true, 'is_posting_allowed' => true],
            ['code' => '1002', 'name_en' => 'Vehicles', 'name_ar' => 'سيارات', 'type' => 'asset', 'parent_code' => '1000', 'is_active' => true, 'is_posting_allowed' => true],

            ['code' => '1100', 'name_en' => 'Current Assets', 'name_ar' => 'الأصول المتداولة', 'type' => 'asset', 'is_active' => true, 'is_posting_allowed' => false],
            ['code' => '1101', 'name_en' => 'Cash in Hand', 'name_ar' => 'النقدية بالصندوق', 'type' => 'asset', 'parent_code' => '1100', 'is_active' => true, 'is_posting_allowed' => true],
            ['code' => '1102', 'name_en' => 'Bank Account', 'name_ar' => 'حساب البنك', 'type' => 'asset', 'parent_code' => '1100', 'is_active' => true, 'is_posting_allowed' => true],
            ['code' => '1103', 'name_en' => 'Accounts Receivable', 'name_ar' => 'المدينون', 'type' => 'asset', 'parent_code' => '1100', 'is_active' => true, 'is_posting_allowed' => true, 'sub_ledger_type' => 'customer'],
            ['code' => '1104', 'name_en' => 'Inventory', 'name_ar' => 'المخزون', 'type' => 'asset', 'parent_code' => '1100', 'is_active' => true, 'is_posting_allowed' => true],

            // Liabilities
            ['code' => '2000', 'name_en' => 'Current Liabilities', 'name_ar' => 'الخصوم المتداولة', 'type' => 'liability', 'is_active' => true, 'is_posting_allowed' => false],
            ['code' => '2001', 'name_en' => 'Accounts Payable', 'name_ar' => 'الدائنون', 'type' => 'liability', 'parent_code' => '2000', 'is_active' => true, 'is_posting_allowed' => true, 'sub_ledger_type' => 'vendor'],
            ['code' => '2002', 'name_en' => 'Tax Payable', 'name_ar' => 'ضريبة مستحقة', 'type' => 'liability', 'parent_code' => '2000', 'is_active' => true, 'is_posting_allowed' => true],

            // Equity
            ['code' => '3000', 'name_en' => 'Equity', 'name_ar' => 'حقوق الملكية', 'type' => 'equity', 'is_active' => true, 'is_posting_allowed' => false],
            ['code' => '3001', 'name_en' => 'Capital', 'name_ar' => 'رأس المال', 'type' => 'equity', 'parent_code' => '3000', 'is_active' => true, 'is_posting_allowed' => true],

            // Revenue
            ['code' => '4000', 'name_en' => 'Sales Revenue', 'name_ar' => 'إيرادات المبيعات', 'type' => 'revenue', 'is_active' => true, 'is_posting_allowed' => true],

            // Expenses
            ['code' => '5000', 'name_en' => 'Operating Expenses', 'name_ar' => 'مصروفات تشغيلية', 'type' => 'expense', 'is_active' => true, 'is_posting_allowed' => false],
            ['code' => '5001', 'name_en' => 'Cost of Goods Sold', 'name_ar' => 'تكلفة البضاعة المباعة', 'type' => 'expense', 'parent_code' => '5000', 'is_active' => true, 'is_posting_allowed' => true],
        ];

        foreach ($accounts as $acc) {
            $parentId = null;
            if (isset($acc['parent_code'])) {
                $parent = ChartOfAccount::where('code', $acc['parent_code'])->first();
                $parentId = $parent?->id;
                unset($acc['parent_code']);
            }

            ChartOfAccount::updateOrCreate(
                ['code' => $acc['code']],
                array_merge($acc, ['parent_id' => $parentId, 'company_id' => 1]) // Default to company 1
            );
        }
    }
}
