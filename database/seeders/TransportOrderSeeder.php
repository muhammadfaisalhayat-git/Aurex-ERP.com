<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransportOrder;
use App\Models\TransportOrderItem;
use Carbon\Carbon;

class TransportOrderSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $order = TransportOrder::create([
                'document_number' => 'TO-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_date' => Carbon::now()->subDays(rand(1, 30)),
                'trailer_id' => rand(1, 4),
                'branch_id' => 1,
                'route_from' => ['Riyadh', 'Jeddah', 'Dammam'][rand(0, 2)],
                'route_to' => ['Jeddah', 'Dammam', 'Riyadh'][rand(0, 2)],
                'scheduled_date' => Carbon::now()->addDays(rand(1, 7)),
                'completion_date' => rand(0, 10) > 7 ? Carbon::now() : null,
                'status' => ['draft', 'loading', 'in_transit', 'delivered'][rand(0, 3)],
                'notes' => 'Transport order #' . $i,
                'created_by' => 4,
            ]);

            // Add 2-4 items
            $numItems = rand(2, 4);
            for ($j = 0; $j < $numItems; $j++) {
                TransportOrderItem::create([
                    'transport_order_id' => $order->id,
                    'product_id' => rand(1, 15),
                    'quantity' => rand(5, 50),
                    'notes' => null,
                ]);
            }
        }
    }
}
