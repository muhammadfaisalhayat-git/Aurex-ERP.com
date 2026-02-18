<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Purchases (Main)
        $purchases = \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '5000'],
            [
                'name_en' => 'Purchases',
                'name_ar' => 'Mosh tarayat',
                'type' => 'expense',
                'parent_id' => null,
                'is_posting_allowed' => false,
                'is_active' => true,
            ]
        );

        // Vendor Purchases (Sub)
        \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '5100'],
            [
                'name_en' => 'Vendor Purchases',
                'name_ar' => 'Mosh tarayat mawridin',
                'type' => 'expense',
                'parent_id' => $purchases->id,
                'is_posting_allowed' => true,
                'is_active' => true,
            ]
        );

        // Local Purchases (Sub)
        \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '5200'],
            [
                'name_en' => 'Local Purchases',
                'name_ar' => 'Mosh tarayat mahaliya',
                'type' => 'expense',
                'parent_id' => $purchases->id,
                'is_posting_allowed' => true,
                'is_active' => true,
            ]
        );

        // Sales (Main)
        $sales = \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '4000'],
            [
                'name_en' => 'Sales',
                'name_ar' => 'Mabe eat',
                'type' => 'revenue',
                'parent_id' => null,
                'is_posting_allowed' => false,
                'is_active' => true,
            ]
        );

        // Product Sales (Sub)
        \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '4100'],
            [
                'name_en' => 'Product Sales',
                'name_ar' => 'Mabe eat montajat',
                'type' => 'revenue',
                'parent_id' => $sales->id,
                'is_posting_allowed' => true,
                'is_active' => true,
            ]
        );

        // Cash (Main - and Posting? usually cash is posting allowed, but can have sub-cashes)
        // Let's make Cash a Main account that is also posting allowed for simplicity, or have sub-chashes.
        // Requirement: "Select Main -> Select Sub". If Main is posting allowed but has no children, logic handles it?
        // Let's create Cash as Main, and Petty Cash as Sub.

        $cash = \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '1000'],
            [
                'name_en' => 'Cash & Equivalents',
                'name_ar' => 'Naqdiya',
                'type' => 'asset',
                'parent_id' => null,
                'is_posting_allowed' => false,
                'is_active' => true,
            ]
        );

        \App\Models\ChartOfAccount::firstOrCreate(
            ['code' => '1100'],
            [
                'name_en' => 'Main Cash',
                'name_ar' => 'Al Naqdiya Al Raisiya',
                'type' => 'asset',
                'parent_id' => $cash->id,
                'is_posting_allowed' => true,
                'is_active' => true,
            ]
        );
    }
}
