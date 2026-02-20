<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'code' => 'MAIN',
                'name_en' => 'Main Branch',
                'name_ar' => 'الفرع الرئيسي',
                'address' => '123 Main Street, Riyadh',
                'phone' => '+966 11 123 4567',
                'email' => 'main@aurex.com',
                'manager_name' => 'Ahmed Al-Rashid',
                'is_active' => true,
            ],
            [
                'code' => 'JED',
                'name_en' => 'Jeddah Branch',
                'name_ar' => 'فرع جدة',
                'address' => '456 King Road, Jeddah',
                'phone' => '+966 12 234 5678',
                'email' => 'jeddah@aurex.com',
                'manager_name' => 'Khalid Al-Otaibi',
                'is_active' => true,
            ],
            [
                'code' => 'DMM',
                'name_en' => 'Dammam Branch',
                'name_ar' => 'فرع الدمام',
                'address' => '789 Industrial Area, Dammam',
                'phone' => '+966 13 345 6789',
                'email' => 'dammam@aurex.com',
                'manager_name' => 'Faisal Al-Qahtani',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['code' => $branch['code']], $branch);
        }
    }
}
