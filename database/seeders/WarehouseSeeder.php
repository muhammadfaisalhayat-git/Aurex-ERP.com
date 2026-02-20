<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'code' => 'WH-RIY-01',
                'name_en' => 'Riyadh Main Warehouse',
                'name_ar' => 'المستودع الرئيسي - الرياض',
                'branch_id' => 1,
                'location' => 'Industrial City, Riyadh',
                'manager_name' => 'Mohammed Al-Saud',
                'is_active' => true,
            ],
            [
                'code' => 'WH-RIY-02',
                'name_en' => 'Riyadh South Warehouse',
                'name_ar' => 'مستودع الرياض الجنوبي',
                'branch_id' => 1,
                'location' => 'South Riyadh',
                'manager_name' => 'Saad Al-Harbi',
                'is_active' => true,
            ],
            [
                'code' => 'WH-JED-01',
                'name_en' => 'Jeddah Main Warehouse',
                'name_ar' => 'المستودع الرئيسي - جدة',
                'branch_id' => 2,
                'location' => 'King Abdulaziz Port Area',
                'manager_name' => 'Yasser Al-Zahrani',
                'is_active' => true,
            ],
            [
                'code' => 'WH-DMM-01',
                'name_en' => 'Dammam Main Warehouse',
                'name_ar' => 'المستودع الرئيسي - الدمام',
                'branch_id' => 3,
                'location' => '2nd Industrial City, Dammam',
                'manager_name' => 'Nasser Al-Dosari',
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(['code' => $warehouse['code']], $warehouse);
        }
    }
}
