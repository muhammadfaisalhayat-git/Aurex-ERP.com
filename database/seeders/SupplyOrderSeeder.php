<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupplyOrder;
use App\Models\SupplyOrderItem;
use Carbon\Carbon;

class SupplyOrderSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $orderDate = Carbon::now()->subDays(rand(1, 45));
            
            $supplyOrder = SupplyOrder::create([
                'document_number' => 'SO-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_number' => 'SUP-' . date('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_date' => $orderDate,
                'expected_delivery_date' => $orderDate->copy()->addDays(14),
                'vendor_id' => rand(1, 5),
                'branch_id' => 1,
                'warehouse_id' => rand(1, 4),
                'status' => ['draft', 'sent', 'partial', 'received'][rand(0, 3)],
                'shipping_amount' => rand(100, 500),
                'terms_conditions' => 'Standard payment terms: Net 30 days',
                'notes' => 'Supply order #' . $i,
                'created_by' => 3,
            ]);

            // Add 2-4 items
            $numItems = rand(2, 4);
            $subtotal = 0;
            $discountAmount = 0;
            $taxAmount = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $quantity = rand(10, 100);
                $unitPrice = rand(10, 200);
                $discountPercentage = rand(0, 5);
                $taxRate = 15;

                $lineTotal = $quantity * $unitPrice;
                $itemDiscount = $lineTotal * ($discountPercentage / 100);
                $taxableAmount = $lineTotal - $itemDiscount;
                $itemTax = $taxableAmount * ($taxRate / 100);
                $totalAmount = $taxableAmount + $itemTax;

                SupplyOrderItem::create([
                    'supply_order_id' => $supplyOrder->id,
                    'product_id' => rand(1, 20),
                    'description' => 'Product for supply order',
                    'quantity' => $quantity,
                    'received_quantity' => rand(0, $quantity),
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => round($itemDiscount, 2),
                    'tax_rate' => $taxRate,
                    'tax_amount' => round($itemTax, 2),
                    'total_amount' => round($totalAmount, 2),
                ]);

                $subtotal += $totalAmount;
                $discountAmount += $itemDiscount;
                $taxAmount += $itemTax;
            }

            $supplyOrder->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal + $supplyOrder->shipping_amount,
            ]);
        }
    }
}
