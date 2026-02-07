<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerRequest;
use App\Models\CustomerRequestItem;
use Carbon\Carbon;

class CustomerRequestSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $request = CustomerRequest::create([
                'document_number' => 'CR-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'request_date' => Carbon::now()->subDays(rand(1, 45)),
                'customer_id' => rand(1, 10),
                'branch_id' => 1,
                'needed_date' => Carbon::now()->addDays(rand(1, 14)),
                'status' => rand(0, 10) > 7 ? 'converted' : 'draft',
                'notes' => 'Customer request #' . $i,
                'created_by' => 5,
            ]);

            // Add 1-3 items
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                CustomerRequestItem::create([
                    'customer_request_id' => $request->id,
                    'product_id' => rand(1, 15),
                    'quantity' => rand(1, 20),
                    'notes' => null,
                ]);
            }
        }
    }
}
