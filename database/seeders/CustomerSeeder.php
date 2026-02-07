<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerGroup;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Customer Groups
        CustomerGroup::create(['name_en' => 'Retail', 'name_ar' => 'تجزئة', 'discount_percentage' => 0]);
        CustomerGroup::create(['name_en' => 'Wholesale', 'name_ar' => 'جملة', 'discount_percentage' => 10]);
        CustomerGroup::create(['name_en' => 'VIP', 'name_ar' => 'VIP', 'discount_percentage' => 15]);
        CustomerGroup::create(['name_en' => 'Corporate', 'name_ar' => 'شركات', 'discount_percentage' => 5]);

        $customers = [
            ['code' => 'CUST-001', 'name_en' => 'Al-Rashid Trading Co.', 'name_ar' => 'شركة الرشيد التجارية', 'group_id' => 4, 'branch_id' => 1, 'contact_person' => 'Ahmed Al-Rashid', 'phone' => '+966 11 111 1111', 'mobile' => '+966 50 111 1111', 'email' => 'ahmed@alrashid.com', 'address' => 'Riyadh Business District', 'city' => 'Riyadh', 'credit_limit' => 100000, 'credit_days' => 30, 'opening_balance' => 0],
            ['code' => 'CUST-002', 'name_en' => 'Saudi Electronics Ltd.', 'name_ar' => 'السعودية للإلكترونيات المحدودة', 'group_id' => 4, 'branch_id' => 1, 'contact_person' => 'Khalid Al-Otaibi', 'phone' => '+966 11 222 2222', 'mobile' => '+966 50 222 2222', 'email' => 'khalid@saudielec.com', 'address' => 'Olaya District, Riyadh', 'city' => 'Riyadh', 'credit_limit' => 150000, 'credit_days' => 45, 'opening_balance' => 0],
            ['code' => 'CUST-003', 'name_en' => 'Modern Furniture Store', 'name_ar' => 'متجر الأثاث العصري', 'group_id' => 2, 'branch_id' => 1, 'contact_person' => 'Faisal Al-Qahtani', 'phone' => '+966 11 333 3333', 'mobile' => '+966 50 333 3333', 'email' => 'faisal@modernfurniture.com', 'address' => 'Al-Malaz, Riyadh', 'city' => 'Riyadh', 'credit_limit' => 50000, 'credit_days' => 30, 'opening_balance' => 0],
            ['code' => 'CUST-004', 'name_en' => 'Jeddah Office Supplies', 'name_ar' => 'مستلزمات مكاتب جدة', 'group_id' => 2, 'branch_id' => 2, 'contact_person' => 'Omar Al-Zahrani', 'phone' => '+966 12 444 4444', 'mobile' => '+966 50 444 4444', 'email' => 'omar@jeddahsupplies.com', 'address' => 'Al-Balad, Jeddah', 'city' => 'Jeddah', 'credit_limit' => 75000, 'credit_days' => 30, 'opening_balance' => 0],
            ['code' => 'CUST-005', 'name_en' => 'Dammam Industrial Tools', 'name_ar' => 'أدوات الدمام الصناعية', 'group_id' => 3, 'branch_id' => 3, 'contact_person' => 'Yasser Al-Dosari', 'phone' => '+966 13 555 5555', 'mobile' => '+966 50 555 5555', 'email' => 'yasser@dammamtools.com', 'address' => 'Industrial City, Dammam', 'city' => 'Dammam', 'credit_limit' => 200000, 'credit_days' => 60, 'opening_balance' => 0],
            ['code' => 'CUST-006', 'name_en' => 'Najran Packaging Solutions', 'name_ar' => 'حلول تغليف نجران', 'group_id' => 2, 'branch_id' => 1, 'contact_person' => 'Saad Al-Harbi', 'phone' => '+966 17 666 6666', 'mobile' => '+966 50 666 6666', 'email' => 'saad@najranpack.com', 'address' => 'Najran Industrial Area', 'city' => 'Najran', 'credit_limit' => 80000, 'credit_days' => 30, 'opening_balance' => 0],
            ['code' => 'CUST-007', 'name_en' => 'Tabuk Trading Establishment', 'name_ar' => 'مؤسسة تبوك التجارية', 'group_id' => 1, 'branch_id' => 2, 'contact_person' => 'Mohammed Al-Saud', 'phone' => '+966 14 777 7777', 'mobile' => '+966 50 777 7777', 'email' => 'mohammed@tabuktrading.com', 'address' => 'Tabuk City Center', 'city' => 'Tabuk', 'credit_limit' => 40000, 'credit_days' => 15, 'opening_balance' => 0],
            ['code' => 'CUST-008', 'name_en' => 'Al-Khobar Tech Solutions', 'name_ar' => 'حلول الخبر التقنية', 'group_id' => 4, 'branch_id' => 3, 'contact_person' => 'Abdullah Al-Rashid', 'phone' => '+966 13 888 8888', 'mobile' => '+966 50 888 8888', 'email' => 'abdullah@khobartech.com', 'address' => 'Al-Khobar Corniche', 'city' => 'Al-Khobar', 'credit_limit' => 120000, 'credit_days' => 45, 'opening_balance' => 0],
            ['code' => 'CUST-009', 'name_en' => 'Makkah Stationery House', 'name_ar' => 'بيت مكة للقرطاسية', 'group_id' => 1, 'branch_id' => 2, 'contact_person' => 'Ibrahim Al-Amoudi', 'phone' => '+966 12 999 9999', 'mobile' => '+966 50 999 9999', 'email' => 'ibrahim@makkahstationery.com', 'address' => 'Al-Aziziyah, Makkah', 'city' => 'Makkah', 'credit_limit' => 30000, 'credit_days' => 15, 'opening_balance' => 0],
            ['code' => 'CUST-010', 'name_en' => 'Madina Furniture Gallery', 'name_ar' => 'معرض المدينة للأثاث', 'group_id' => 3, 'branch_id' => 2, 'contact_person' => 'Nasser Al-Otaibi', 'phone' => '+966 14 000 0000', 'mobile' => '+966 50 000 0000', 'email' => 'nasser@madinafurniture.com', 'address' => 'Al-Haram, Madina', 'city' => 'Madina', 'credit_limit' => 90000, 'credit_days' => 30, 'opening_balance' => 0],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
