<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransportClaim;
use Carbon\Carbon;

class TransportClaimSeeder extends Seeder
{
    public function run(): void
    {
        $claims = [
            [
                'claim_number' => 'TCM-00001',
                'claim_date' => Carbon::now()->subDays(15),
                'transport_order_id' => 1,
                'claim_type' => 'damage',
                'description' => 'Product damaged during transport due to improper handling',
                'claim_amount' => 5000,
                'status' => 'under_review',
                'created_by' => 4,
            ],
            [
                'claim_number' => 'TCM-00002',
                'claim_date' => Carbon::now()->subDays(10),
                'transport_order_id' => 2,
                'claim_type' => 'delay',
                'description' => 'Delivery delayed by 3 days causing production stoppage',
                'claim_amount' => 2500,
                'status' => 'approved',
                'settled_amount' => 2000,
                'created_by' => 4,
            ],
            [
                'claim_number' => 'TCM-00003',
                'claim_date' => Carbon::now()->subDays(5),
                'transport_order_id' => 3,
                'claim_type' => 'loss',
                'description' => '2 cartons missing from delivery',
                'claim_amount' => 1500,
                'status' => 'settled',
                'settled_amount' => 1500,
                'created_by' => 4,
            ],
        ];

        foreach ($claims as $claim) {
            TransportClaim::create($claim);
        }
    }
}
