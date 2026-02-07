<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Carbon\Carbon;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $orderDate = Carbon::now()->subDays(rand(1, 45));
            $status = ['draft', 'confirmed', 'processing', 'partial', 'shipped', 'delivered', 'invoiced'][rand(0, 6)];
            
            $salesOrder = SalesOrder::create([
                'document_number' => 'SLO-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_number' => 'SO-' . date('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_date' => $orderDate,
                'expected_delivery_date' => $orderDate->copy()->addDays(7),
                'delivery_date' => in_array($status, ['delivered', 'invoiced']) ? $orderDate->copy()->addDays(rand(5, 10)) : null,
                'customer_id' => rand(1, 10),
                'branch_id' => 1,
                'warehouse_id' => rand(1, 4),
                'salesman_id' => 5,
                'quotation_id' => rand(0, 10) > 5 ? rand(1, 20) : null,
                'status' => $status,
                'shipping_amount' => rand(50, 300),
                'delivery_address' => 'Customer delivery address',
                'terms_conditions' => 'Standard delivery terms',
                'notes' => 'Sales order #' . $i,
                'created_by' => 4,
            ]);

            // Add 2-5 items
            $numItems = rand(2, 5);
            $subtotal = 0;
            $discountAmount = 0;
            $taxAmount = 0;
            $netAmount = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $quantity = rand(1, 20);
                $unitPrice = rand(50, 1000);
                $discountPercentage = rand(0, 10);
                $taxRate = 15;

                // Tax-inclusive calculation
                $grossAmount = ($unitPrice * $quantity) * (1 - $discountPercentage / 100);
                $itemDiscount = ($unitPrice * $quantity) * ($discountPercentage / 100);
                $itemNet = $grossAmount / (1 + $taxRate / 100);
                $itemTax = $grossAmount - $itemNet;

                $invoicedQty = in_array($status, ['invoiced']) ? $quantity : rand(0, $quantity);
                $deliveredQty = in_array($status, ['delivered', 'invoiced']) ? $quantity : rand(0, $quantity);

                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => rand(1, 15),
                    'description' => 'Product for sales order',
                    'quantity' => $quantity,
                    'delivered_quantity' => $deliveredQty,
                    'invoiced_quantity' => $invoicedQty,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => round($itemDiscount, 2),
                    'gross_amount' => round($grossAmount, 2),
                    'tax_rate' => $taxRate,
                    'tax_amount' => round($itemTax, 2),
                    'net_amount' => round($itemNet, 2),
                ]);

                $subtotal += $grossAmount;
                $discountAmount += $itemDiscount;
                $taxAmount += $itemTax;
                $netAmount += $itemNet;
            }

            $totalAmount = $netAmount + $salesOrder->shipping_amount;
            $invoicedAmount = $status === 'invoiced' ? $totalAmount : rand(0, (int)($totalAmount / 2));

            $salesOrder->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'invoiced_amount' => $invoicedAmount,
                'balance_amount' => $totalAmount - $invoicedAmount,
            ]);
        }
    }
}
