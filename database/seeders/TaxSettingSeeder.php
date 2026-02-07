<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxSetting;

class TaxSettingSeeder extends Seeder
{
    public function run(): void
    {
        TaxSetting::create([
            'tax_enabled' => true,
            'default_tax_rate' => 15.00,
            'rounding_mode' => 'per_line',
            'tax_name_en' => 'VAT',
            'tax_name_ar' => 'ضريبة القيمة المضافة',
            'tax_number' => '310123456700003',
        ]);
    }
}
