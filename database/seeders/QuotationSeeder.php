<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Carbon\Carbon;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $quotationDate = Carbon::now()->subDays(rand(1, 60));
            $quotation = Quotation::create([
                'document_number' => 'QT-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'quotation_date' => $quotationDate,
                'expiry_date' => $quotationDate->copy()->addDays(30),
                'customer_id' => rand(1, 10),
                'branch_id' => 1,
                'warehouse_id' => rand(1, 4),
                'salesman_id' => 5,
                'status' => ['draft', 'sent', 'accepted', 'expired'][rand(0, 3)],
                'version' => 1,
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_rate' => 15,
                'tax_amount' => 0,
                'total_amount' => 0,
                'notes' => 'Quotation #' . $i,
                'created_by' => 5,
            ]);

            // Add 2-4 items
            $numItems = rand(2, 4);
            $subtotal = 0;
            $totalTax = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $quantity = rand(1, 10);
                $unitPrice = rand(50, 1000);
                $lineTotal = $quantity * $unitPrice;
                $taxAmount = $lineTotal * 0.15 / 1.15;
                $netAmount = $lineTotal - $taxAmount;
                
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => rand(1, 15),
                    'description' => 'Product description',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => 0,
                    'discount_amount' => 0,
                    'tax_rate' => 15,
                    'tax_amount' => round($taxAmount, 2),
                    'net_amount' => round($netAmount, 2),
                    'gross_amount' => $lineTotal,
                ]);
                
                $subtotal += $lineTotal;
                $totalTax += $taxAmount;
            }

            $quotation->update([
                'subtotal' => $subtotal,
                'tax_amount' => round($totalTax, 2),
                'total_amount' => round($subtotal, 2),
            ]);
        }
    }
}
