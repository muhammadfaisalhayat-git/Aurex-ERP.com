<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $types = [
            ['code' => 'asset', 'name_en' => 'Asset', 'name_ar' => 'أصول'],
            ['code' => 'liability', 'name_en' => 'Liability', 'name_ar' => 'خصوم'],
            ['code' => 'equity', 'name_en' => 'Equity', 'name_ar' => 'حقوق ملكية'],
            ['code' => 'revenue', 'name_en' => 'Revenue', 'name_ar' => 'إيرادات'],
            ['code' => 'expense', 'name_en' => 'Expense', 'name_ar' => 'مصروفات'],
        ];

        foreach ($types as $type) {
            \App\Models\AccountType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'name_en' => $type['name_en'],
                    'name_ar' => $type['name_ar'],
                    'is_active' => true,
                ]
            );
        }
    }
}
