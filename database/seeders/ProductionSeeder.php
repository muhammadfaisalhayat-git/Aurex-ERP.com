<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Production\WorkCenter;
use App\Models\Production\Machine;
use App\Models\Production\ProductionOrder;
use App\Models\Product;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        $branch = Branch::first();
        if (!$branch)
            return;

        $admin = User::first();

        // 1. Create Work Centers
        $metalWC = WorkCenter::updateOrCreate(
        ['code' => 'WC-METAL', 'company_id' => $company->id],
        [
            'name' => 'Metal Fabrication Shop',
            'is_active' => true,
            'branch_id' => $branch->id
        ]
        );

        $assemblyWC = WorkCenter::updateOrCreate(
        ['code' => 'WC-ASSY', 'company_id' => $company->id],
        [
            'name' => 'Final Assembly Line',
            'is_active' => true,
            'branch_id' => $branch->id
        ]
        );

        // 2. Create Machines
        Machine::updateOrCreate(
        ['code' => 'MAC-CNC-01', 'company_id' => $company->id],
        [
            'work_center_id' => $metalWC->id,
            'name' => 'CNC Lathe Maschine',
            'brand' => 'Haas',
            'model' => 'ST-20',
            'hourly_cost' => 45.00,
            'status' => 'available',
        ]
        );

        Machine::updateOrCreate(
        ['code' => 'MAC-LASER-01', 'company_id' => $company->id],
        [
            'work_center_id' => $metalWC->id,
            'name' => 'Laser Cutter',
            'brand' => 'Trumpf',
            'model' => 'TruLaser 3030',
            'hourly_cost' => 120.00,
            'status' => 'available',
        ]
        );

        Machine::updateOrCreate(
        ['code' => 'MAC-ASSY-ROBOT', 'company_id' => $company->id],
        [
            'work_center_id' => $assemblyWC->id,
            'name' => 'Assembly Robot Arm',
            'brand' => 'Universal Robots',
            'model' => 'UR10e',
            'hourly_cost' => 30.00,
            'status' => 'busy',
        ]
        );

        // 3. Create Production Orders
        $products = Product::limit(3)->get();
        if ($products->count() > 0) {
            foreach ($products as $index => $product) {
                ProductionOrder::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'document_number' => 'PO-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                ],
                [
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'quantity' => 100 + ($index * 50),
                    'status' => $index === 0 ? 'completed' : 'in_progress',
                    'start_date' => now()->subDays(5),
                    'end_date' => $index === 0 ? now()->subDay() : null,
                    'unit_cost' => 15.50 * ($index + 1),
                    'total_cost' => (100 + ($index * 50)) * (15.50 * ($index + 1)),
                    'created_by' => $admin->id ?? 1,
                ]
                );
            }
        }
    }
}
