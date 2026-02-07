<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommissionRule;
use Carbon\Carbon;

class CommissionRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name_en' => 'Standard Sales Commission',
                'name_ar' => 'عمولة المبيعات القياسية',
                'salesman_id' => 5,
                'calculation_type' => 'percentage',
                'commission_value' => 5.00,
                'min_sales_amount' => 0,
                'max_sales_amount' => null,
                'effective_from' => Carbon::now()->subMonths(6),
                'effective_to' => null,
                'is_active' => true,
            ],
            [
                'name_en' => 'VIP Customer Commission',
                'name_ar' => 'عمولة العملاء VIP',
                'customer_group_id' => 3,
                'calculation_type' => 'percentage',
                'commission_value' => 7.50,
                'min_sales_amount' => 10000,
                'max_sales_amount' => null,
                'effective_from' => Carbon::now()->subMonths(3),
                'effective_to' => null,
                'is_active' => true,
            ],
            [
                'name_en' => 'Electronics Category Bonus',
                'name_ar' => 'مكافأة فئة الإلكترونيات',
                'product_category_id' => 1,
                'calculation_type' => 'percentage',
                'commission_value' => 3.00,
                'min_sales_amount' => 5000,
                'max_sales_amount' => 50000,
                'effective_from' => Carbon::now()->subMonths(1),
                'effective_to' => null,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            CommissionRule::create($rule);
        }
    }
}
