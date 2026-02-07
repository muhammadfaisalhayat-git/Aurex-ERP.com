<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['code' => 'VEND-001', 'name_en' => 'Dell Saudi Arabia', 'name_ar' => 'ديل السعودية', 'branch_id' => 1, 'contact_person' => 'John Smith', 'phone' => '+966 11 100 1000', 'mobile' => '+966 50 100 1000', 'email' => 'sales@dell.sa', 'address' => 'King Fahd Road, Riyadh', 'city' => 'Riyadh', 'opening_balance' => 0],
            ['code' => 'VEND-002', 'name_en' => 'HP Middle East', 'name_ar' => 'HP الشرق الأوسط', 'branch_id' => 1, 'contact_person' => 'Sarah Johnson', 'phone' => '+966 11 200 2000', 'mobile' => '+966 50 200 2000', 'email' => 'sales@hp.me', 'address' => 'Tahlia Street, Riyadh', 'city' => 'Riyadh', 'opening_balance' => 0],
            ['code' => 'VEND-003', 'name_en' => 'IKEA Saudi Arabia', 'name_ar' => 'ايكيا السعودية', 'branch_id' => 2, 'contact_person' => 'Anna Karlsson', 'phone' => '+966 12 300 3000', 'mobile' => '+966 50 300 3000', 'email' => 'b2b@ikea.sa', 'address' => 'Red Sea Mall, Jeddah', 'city' => 'Jeddah', 'opening_balance' => 0],
            ['code' => 'VEND-004', 'name_en' => 'Saudi Paper Manufacturing', 'name_ar' => 'السعودية لصناعة الورق', 'branch_id' => 3, 'contact_person' => 'Mohammed Al-Ali', 'phone' => '+966 13 400 4000', 'mobile' => '+966 50 400 4000', 'email' => 'sales@saudipaper.com', 'address' => 'Dammam 2nd Industrial City', 'city' => 'Dammam', 'opening_balance' => 0],
            ['code' => 'VEND-005', 'name_en' => 'Bosch Power Tools KSA', 'name_ar' => 'بوش للأدوات الكهربائية', 'branch_id' => 1, 'contact_person' => 'Hans Mueller', 'phone' => '+966 11 500 5000', 'mobile' => '+966 50 500 5000', 'email' => 'sales@bosch.sa', 'address' => 'Sulay District, Riyadh', 'city' => 'Riyadh', 'opening_balance' => 0],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
