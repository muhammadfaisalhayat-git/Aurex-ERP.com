<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use Carbon\Carbon;

class SalesReturnSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $return = SalesReturn::create([
                'document_number' => 'SR-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'return_number' => 'RET-' . date('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'return_date' => Carbon::now()->subDays(rand(1, 30)),
                'sales_invoice_id' => $i,
                'customer_id' => rand(1, 10),
                'branch_id' => 1,
                'warehouse_id' => rand(1, 4),
                'status' => rand(0, 10) > 5 ? 'posted' : 'draft',
                'return_reason' => ['defective', 'wrong_item', 'customer_return'][rand(0, 2)],
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'notes' => 'Return #' . $i,
                'created_by' => 4,
            ]);

            // Add 1-2 items
            $numItems = rand(1, 2);
            $subtotal = 0;
            $totalTax = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $quantity = rand(1, 3);
                $unitPrice = rand(50, 500);
                $lineTotal = $quantity * $unitPrice;
                $taxAmount = $lineTotal * 0.15 / 1.15;
                
                SalesReturnItem::create([
                    'sales_return_id' => $return->id,
                    'product_id' => rand(1, 15),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_amount' => round($taxAmount, 2),
                    'total_amount' => $lineTotal,
                ]);
                
                $subtotal += $lineTotal;
                $totalTax += $taxAmount;
            }

            $return->update([
                'subtotal' => $subtotal,
                'tax_amount' => round($totalTax, 2),
                'total_amount' => round($subtotal, 2),
            ]);
        }
    }
}
