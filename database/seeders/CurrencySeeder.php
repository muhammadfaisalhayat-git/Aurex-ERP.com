<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'SAR', 'name_en' => 'Saudi Riyal', 'name_ar' => 'ريال سعودي', 'symbol' => '﷼', 'exchange_rate' => 1.000000, 'is_default' => true, 'is_active' => true],
            ['code' => 'USD', 'name_en' => 'US Dollar', 'name_ar' => 'دولار أمريكي', 'symbol' => '$', 'exchange_rate' => 3.750000, 'is_default' => false, 'is_active' => true],
            ['code' => 'EUR', 'name_en' => 'Euro', 'name_ar' => 'يورو', 'symbol' => '€', 'exchange_rate' => 4.050000, 'is_default' => false, 'is_active' => true],
            ['code' => 'AED', 'name_en' => 'UAE Dirham', 'name_ar' => 'درهم إماراتي', 'symbol' => 'د.إ', 'exchange_rate' => 1.020000, 'is_default' => false, 'is_active' => true],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
