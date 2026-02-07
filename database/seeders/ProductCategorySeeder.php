<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'ELEC',
                'name_en' => 'Electronics',
                'name_ar' => 'إلكترونيات',
                'description' => 'Electronic devices and components',
                'is_active' => true,
            ],
            [
                'code' => 'FURN',
                'name_en' => 'Furniture',
                'name_ar' => 'أثاث',
                'description' => 'Office and home furniture',
                'is_active' => true,
            ],
            [
                'code' => 'STATION',
                'name_en' => 'Stationery',
                'name_ar' => 'قرطاسية',
                'description' => 'Office supplies and stationery',
                'is_active' => true,
            ],
            [
                'code' => 'TOOLS',
                'name_en' => 'Tools & Equipment',
                'name_ar' => 'أدوات ومعدات',
                'description' => 'Industrial and hand tools',
                'is_active' => true,
            ],
            [
                'code' => 'PACK',
                'name_en' => 'Packaging Materials',
                'name_ar' => 'مواد تغليف',
                'description' => 'Packaging and shipping materials',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
