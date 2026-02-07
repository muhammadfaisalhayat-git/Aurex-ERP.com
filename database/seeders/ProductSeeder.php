<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductBom;
use App\Models\StockBalance;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Electronics
            ['code' => 'LAPTOP-001', 'name_en' => 'Laptop Dell Latitude', 'name_ar' => 'لابتوب ديل لاتيتيود', 'category_id' => 1, 'type' => 'simple', 'barcode' => '1234567890123', 'cost_price' => 2500, 'sale_price' => 2999, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 5, 'reorder_quantity' => 10, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'MONITOR-001', 'name_en' => 'Monitor 24 inch LED', 'name_ar' => 'شاشة 24 بوصة LED', 'category_id' => 1, 'type' => 'simple', 'barcode' => '1234567890124', 'cost_price' => 400, 'sale_price' => 499, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 10, 'reorder_quantity' => 20, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'KEYBOARD-001', 'name_en' => 'Wireless Keyboard', 'name_ar' => 'لوحة مفاتيح لاسلكية', 'category_id' => 1, 'type' => 'simple', 'barcode' => '1234567890125', 'cost_price' => 80, 'sale_price' => 99, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 20, 'reorder_quantity' => 50, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'MOUSE-001', 'name_en' => 'Wireless Mouse', 'name_ar' => 'فأرة لاسلكية', 'category_id' => 1, 'type' => 'simple', 'barcode' => '1234567890126', 'cost_price' => 40, 'sale_price' => 49, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 30, 'reorder_quantity' => 100, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'PRINTER-001', 'name_en' => 'Laser Printer', 'name_ar' => 'طابعة ليزر', 'category_id' => 1, 'type' => 'simple', 'barcode' => '1234567890127', 'cost_price' => 600, 'sale_price' => 749, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 3, 'reorder_quantity' => 10, 'is_sellable' => true, 'is_purchasable' => true],
            
            // Furniture
            ['code' => 'DESK-001', 'name_en' => 'Office Desk 120cm', 'name_ar' => 'مكتب مكتبي 120 سم', 'category_id' => 2, 'type' => 'simple', 'barcode' => '2234567890123', 'cost_price' => 350, 'sale_price' => 449, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 5, 'reorder_quantity' => 10, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'CHAIR-001', 'name_en' => 'Ergonomic Office Chair', 'name_ar' => 'كرسي مكتبي مريح', 'category_id' => 2, 'type' => 'simple', 'barcode' => '2234567890124', 'cost_price' => 450, 'sale_price' => 599, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 8, 'reorder_quantity' => 15, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'CABINET-001', 'name_en' => 'Filing Cabinet', 'name_ar' => 'خزنة ملفات', 'category_id' => 2, 'type' => 'simple', 'barcode' => '2234567890125', 'cost_price' => 200, 'sale_price' => 279, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 5, 'reorder_quantity' => 10, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'SHELF-001', 'name_en' => 'Bookshelf 5-tier', 'name_ar' => 'رف كتب 5 أدوار', 'category_id' => 2, 'type' => 'simple', 'barcode' => '2234567890126', 'cost_price' => 150, 'sale_price' => 199, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 5, 'reorder_quantity' => 10, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'TABLE-001', 'name_en' => 'Meeting Table', 'name_ar' => 'طاولة اجتماعات', 'category_id' => 2, 'type' => 'simple', 'barcode' => '2234567890127', 'cost_price' => 800, 'sale_price' => 999, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 2, 'reorder_quantity' => 5, 'is_sellable' => true, 'is_purchasable' => true],
            
            // Stationery
            ['code' => 'PAPER-A4', 'name_en' => 'A4 Copy Paper (500 sheets)', 'name_ar' => 'ورق A4 (500 ورقة)', 'category_id' => 3, 'type' => 'simple', 'barcode' => '3234567890123', 'cost_price' => 15, 'sale_price' => 22, 'tax_rate' => 15, 'unit_of_measure' => 'ream', 'reorder_level' => 100, 'reorder_quantity' => 500, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'PEN-BALL', 'name_en' => 'Ballpoint Pen (Box of 12)', 'name_ar' => 'قلم حبر (علبة 12)', 'category_id' => 3, 'type' => 'simple', 'barcode' => '3234567890124', 'cost_price' => 8, 'sale_price' => 15, 'tax_rate' => 15, 'unit_of_measure' => 'box', 'reorder_level' => 50, 'reorder_quantity' => 200, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'NOTEBOOK-A5', 'name_en' => 'Notebook A5', 'name_ar' => 'دفتر A5', 'category_id' => 3, 'type' => 'simple', 'barcode' => '3234567890125', 'cost_price' => 3, 'sale_price' => 6, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 100, 'reorder_quantity' => 500, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'STAPLER-001', 'name_en' => 'Desktop Stapler', 'name_ar' => 'دباسة مكتبية', 'category_id' => 3, 'type' => 'simple', 'barcode' => '3234567890126', 'cost_price' => 12, 'sale_price' => 19, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 20, 'reorder_quantity' => 50, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'TAPE-001', 'name_en' => 'Clear Tape (Pack of 6)', 'name_ar' => 'شريط لاصق (عبوة 6)', 'category_id' => 3, 'type' => 'simple', 'barcode' => '3234567890127', 'cost_price' => 5, 'sale_price' => 10, 'tax_rate' => 15, 'unit_of_measure' => 'pack', 'reorder_level' => 50, 'reorder_quantity' => 200, 'is_sellable' => true, 'is_purchasable' => true],
            
            // Tools
            ['code' => 'DRILL-001', 'name_en' => 'Cordless Drill 18V', 'name_ar' => 'مثقاب لاسلكي 18V', 'category_id' => 4, 'type' => 'simple', 'barcode' => '4234567890123', 'cost_price' => 180, 'sale_price' => 249, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 5, 'reorder_quantity' => 15, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'WRENCH-SET', 'name_en' => 'Wrench Set 12pcs', 'name_ar' => 'طقم مفاتيح 12 قطعة', 'category_id' => 4, 'type' => 'simple', 'barcode' => '4234567890124', 'cost_price' => 85, 'sale_price' => 129, 'tax_rate' => 15, 'unit_of_measure' => 'set', 'reorder_level' => 10, 'reorder_quantity' => 30, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'SCREWDRIVER-SET', 'name_en' => 'Screwdriver Set 6pcs', 'name_ar' => 'طقم مفكات 6 قطع', 'category_id' => 4, 'type' => 'simple', 'barcode' => '4234567890125', 'cost_price' => 35, 'sale_price' => 55, 'tax_rate' => 15, 'unit_of_measure' => 'set', 'reorder_level' => 15, 'reorder_quantity' => 50, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'HAMMER-001', 'name_en' => 'Claw Hammer', 'name_ar' => 'مطرقة مخلبية', 'category_id' => 4, 'type' => 'simple', 'barcode' => '4234567890126', 'cost_price' => 25, 'sale_price' => 39, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 20, 'reorder_quantity' => 50, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'LEVEL-001', 'name_en' => 'Spirit Level 60cm', 'name_ar' => 'ميزان ماء 60 سم', 'category_id' => 4, 'type' => 'simple', 'barcode' => '4234567890127', 'cost_price' => 45, 'sale_price' => 69, 'tax_rate' => 15, 'unit_of_measure' => 'piece', 'reorder_level' => 10, 'reorder_quantity' => 25, 'is_sellable' => true, 'is_purchasable' => true],
            
            // Packaging
            ['code' => 'BOX-CARTON-S', 'name_en' => 'Carton Box Small (Pack of 50)', 'name_ar' => 'صندوق كرتون صغير (50 حبة)', 'category_id' => 5, 'type' => 'simple', 'barcode' => '5234567890123', 'cost_price' => 25, 'sale_price' => 45, 'tax_rate' => 15, 'unit_of_measure' => 'pack', 'reorder_level' => 20, 'reorder_quantity' => 100, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'BOX-CARTON-M', 'name_en' => 'Carton Box Medium (Pack of 50)', 'name_ar' => 'صندوق كرتون متوسط (50 حبة)', 'category_id' => 5, 'type' => 'simple', 'barcode' => '5234567890124', 'cost_price' => 35, 'sale_price' => 65, 'tax_rate' => 15, 'unit_of_measure' => 'pack', 'reorder_level' => 20, 'reorder_quantity' => 100, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'TAPE-PACK', 'name_en' => 'Packing Tape (Pack of 6)', 'name_ar' => 'شريط تغليف (عبوة 6)', 'category_id' => 5, 'type' => 'simple', 'barcode' => '5234567890125', 'cost_price' => 12, 'sale_price' => 22, 'tax_rate' => 15, 'unit_of_measure' => 'pack', 'reorder_level' => 30, 'reorder_quantity' => 150, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'BUBBLE-WRAP', 'name_en' => 'Bubble Wrap Roll 50m', 'name_ar' => 'بلاستيك فقاعي 50 متر', 'category_id' => 5, 'type' => 'simple', 'barcode' => '5234567890126', 'cost_price' => 30, 'sale_price' => 55, 'tax_rate' => 15, 'unit_of_measure' => 'roll', 'reorder_level' => 15, 'reorder_quantity' => 50, 'is_sellable' => true, 'is_purchasable' => true],
            ['code' => 'STRETCH-FILM', 'name_en' => 'Stretch Film 500mm', 'name_ar' => 'فيلم تغليف 500 مم', 'category_id' => 5, 'type' => 'simple', 'barcode' => '5234567890127', 'cost_price' => 40, 'sale_price' => 75, 'tax_rate' => 15, 'unit_of_measure' => 'roll', 'reorder_level' => 10, 'reorder_quantity' => 40, 'is_sellable' => true, 'is_purchasable' => true],
            
            // Composite Product (Computer Bundle)
            ['code' => 'BUNDLE-PC-01', 'name_en' => 'Computer Workstation Bundle', 'name_ar' => 'حزمة محطة عمل كمبيوتر', 'category_id' => 1, 'type' => 'composite', 'barcode' => '6234567890123', 'cost_price' => 0, 'sale_price' => 3499, 'tax_rate' => 15, 'unit_of_measure' => 'set', 'reorder_level' => 0, 'reorder_quantity' => 0, 'is_sellable' => true, 'is_purchasable' => false],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create BOM for composite product
        ProductBom::create([
            'product_id' => 21, // Computer Bundle
            'component_id' => 1, // Laptop
            'quantity' => 1,
            'waste_percentage' => 0,
        ]);
        ProductBom::create([
            'product_id' => 21, // Computer Bundle
            'component_id' => 2, // Monitor
            'quantity' => 2,
            'waste_percentage' => 0,
        ]);
        ProductBom::create([
            'product_id' => 21, // Computer Bundle
            'component_id' => 3, // Keyboard
            'quantity' => 1,
            'waste_percentage' => 0,
        ]);
        ProductBom::create([
            'product_id' => 21, // Computer Bundle
            'component_id' => 4, // Mouse
            'quantity' => 1,
            'waste_percentage' => 0,
        ]);

        // Create initial stock balances
        $warehouseIds = [1, 2, 3, 4];
        foreach ($products as $index => $product) {
            if ($product['type'] === 'simple') {
                foreach ($warehouseIds as $warehouseId) {
                    $initialQty = rand(20, 200);
                    StockBalance::create([
                        'product_id' => $index + 1,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $initialQty,
                        'reserved_quantity' => 0,
                        'available_quantity' => $initialQty,
                        'average_cost' => $product['cost_price'],
                    ]);
                }
            }
        }
    }
}
