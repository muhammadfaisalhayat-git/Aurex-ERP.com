<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistics\DeliveryVehicle;
use App\Models\Logistics\FuelLog;
use App\Models\Company;
use App\Models\User;

class LogisticsSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        $admin = User::first();

        // 1. Create Delivery Vehicles
        $truck1 = DeliveryVehicle::updateOrCreate(
        ['plate_number' => 'LGT-1001-KSA', 'company_id' => $company->id],
        [
            'brand' => 'Mercedes-Benz',
            'model' => 'Actros',
            'type' => 'heavy_truck',
            'max_payload' => 25000.00,
            'fuel_type' => 'diesel',
            'status' => 'available',
        ]
        );

        $van = DeliveryVehicle::updateOrCreate(
        ['plate_number' => 'LGT-2005-KSA', 'company_id' => $company->id],
        [
            'brand' => 'Ford',
            'model' => 'Transit',
            'type' => 'van',
            'max_payload' => 3500.00,
            'fuel_type' => 'petrol',
            'status' => 'in_transit',
        ]
        );

        // 2. Create Fuel Logs
        // Using entry_date and odometer_reading as unique-ish identifiers for the log
        FuelLog::updateOrCreate(
        [
            'company_id' => $company->id,
            'delivery_vehicle_id' => $truck1->id,
            'entry_date' => now()->subDays(2)->toDateString(),
            'odometer_reading' => 125400,
        ],
        [
            'liters' => 450.00,
            'cost_per_liter' => 2.10,
            'total_cost' => 945.00,
            'logged_by' => $admin->id,
        ]
        );

        FuelLog::updateOrCreate(
        [
            'company_id' => $company->id,
            'delivery_vehicle_id' => $van->id,
            'entry_date' => now()->subDay()->toDateString(),
            'odometer_reading' => 45800,
        ],
        [
            'liters' => 65.00,
            'cost_per_liter' => 2.30,
            'total_cost' => 149.50,
            'logged_by' => $admin->id,
        ]
        );
    }
}
