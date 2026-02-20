<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceVoucher;
use App\Models\MaintenanceWorkshop;
use App\Models\Logistics\DeliveryVehicle;
use App\Models\Company;
use App\Models\User;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        $admin = User::first();
        $workshop = MaintenanceWorkshop::first();
        $vehicle = DeliveryVehicle::first();

        if (!$workshop || !$vehicle)
            return;

        // 1. Create Maintenance Vouchers
        MaintenanceVoucher::updateOrCreate(
        ['voucher_number' => 'MNT-' . date('Y') . '-0001', 'company_id' => $company->id],
        [
            'workshop_id' => $workshop->id,
            'voucher_date' => now()->subDays(7),
            'entity_type' => 'vehicle',
            'entity_id' => $vehicle->id,
            'entity_name' => $vehicle->plate_number,
            'maintenance_type' => 'corrective',
            'problem_description' => 'Engine oil change and filter replacement. Brake pads inspection.',
            'status' => 'completed',
            'completion_date' => now()->subDays(6),
            'actual_cost' => 850.00,
            'created_by' => $admin->id,
        ]
        );

        MaintenanceVoucher::updateOrCreate(
        ['voucher_number' => 'MNT-' . date('Y') . '-0002', 'company_id' => $company->id],
        [
            'workshop_id' => $workshop->id,
            'voucher_date' => now()->subDays(2),
            'entity_type' => 'vehicle',
            'entity_id' => $vehicle->id,
            'entity_name' => $vehicle->plate_number,
            'maintenance_type' => 'inspection',
            'problem_description' => 'Tire rotation and alignment check. Unusual noise from front suspension.',
            'status' => 'in_progress',
            'estimated_cost' => 450.00,
            'created_by' => $admin->id,
        ]
        );
    }
}
