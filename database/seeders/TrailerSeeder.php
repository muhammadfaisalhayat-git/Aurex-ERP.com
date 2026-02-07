<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trailer;

class TrailerSeeder extends Seeder
{
    public function run(): void
    {
        $trailers = [
            [
                'code' => 'TRL-001',
                'plate_number' => '1234 ABC',
                'trailer_type' => 'Flatbed',
                'capacity_kg' => 25000,
                'driver_name' => 'Ahmed Al-Rashid',
                'driver_phone' => '+966 50 111 1111',
                'license_number' => 'DL-123456',
                'license_expiry' => '2025-12-31',
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'code' => 'TRL-002',
                'plate_number' => '5678 DEF',
                'trailer_type' => 'Box Trailer',
                'capacity_kg' => 20000,
                'driver_name' => 'Khalid Al-Otaibi',
                'driver_phone' => '+966 50 222 2222',
                'license_number' => 'DL-234567',
                'license_expiry' => '2025-11-30',
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'code' => 'TRL-003',
                'plate_number' => '9012 GHI',
                'trailer_type' => 'Refrigerated',
                'capacity_kg' => 18000,
                'driver_name' => 'Faisal Al-Qahtani',
                'driver_phone' => '+966 50 333 3333',
                'license_number' => 'DL-345678',
                'license_expiry' => '2026-01-31',
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'code' => 'TRL-004',
                'plate_number' => '3456 JKL',
                'trailer_type' => 'Flatbed',
                'capacity_kg' => 30000,
                'driver_name' => 'Omar Al-Zahrani',
                'driver_phone' => '+966 50 444 4444',
                'license_number' => 'DL-456789',
                'license_expiry' => '2025-10-31',
                'status' => 'in_use',
                'is_active' => true,
            ],
        ];

        foreach ($trailers as $trailer) {
            Trailer::create($trailer);
        }
    }
}
