<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceWorkshop;

class MaintenanceWorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $workshops = [
            [
                'code' => 'WS-001',
                'name_en' => 'Main Workshop - Riyadh',
                'name_ar' => 'الورشة الرئيسية - الرياض',
                'address' => 'Industrial Area, Riyadh',
                'phone' => '+966 11 123 4567',
                'email' => 'workshop.riyadh@aurex.com',
                'manager_name' => 'Eng. Ahmed Al-Rashid',
                'workshop_type' => 'internal',
                'is_active' => true,
            ],
            [
                'code' => 'WS-002',
                'name_en' => 'Jeddah Service Center',
                'name_ar' => 'مركز خدمة جدة',
                'address' => 'Al-Hamra District, Jeddah',
                'phone' => '+966 12 234 5678',
                'email' => 'workshop.jeddah@aurex.com',
                'manager_name' => 'Eng. Khalid Al-Otaibi',
                'workshop_type' => 'internal',
                'is_active' => true,
            ],
            [
                'code' => 'WS-003',
                'name_en' => 'External Workshop - Dammam',
                'name_ar' => 'ورشة خارجية - الدمام',
                'address' => '3rd Industrial City, Dammam',
                'phone' => '+966 13 345 6789',
                'email' => 'external.dammam@workshop.com',
                'manager_name' => 'Eng. Faisal Al-Qahtani',
                'workshop_type' => 'external',
                'is_active' => true,
            ],
        ];

        foreach ($workshops as $workshop) {
            MaintenanceWorkshop::create($workshop);
        }
    }
}
