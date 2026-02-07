<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\DocumentNumber;
use App\Models\StockLedger;
use Carbon\Carbon;

class SalesInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $products = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $warehouses = [1, 2, 3, 4];
        
        for ($i = 1; $i <= 30; $i++) {
            $invoiceDate = Carbon::now()->subDays(rand(0, 60));
            $customerId = $customers[array_rand($customers)];
            $warehouseId = $warehouses[array_rand($warehouses)];
            
            $invoice = SalesInvoice::create([
                'document_number' => 'SI-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'invoice_date' => $invoiceDate,
                'due_date' => $invoiceDate->copy()->addDays(30),
                'customer_id' => $customerId,
                'branch_id' => 1,
                'warehouse_id' => $warehouseId,
                'salesman_id' => 5,
                'status' => rand(0, 10) > 3 ? 'posted' : 'draft',
                'payment_terms' => 'credit',
                'tax_rate' => 15.00,
                'notes' => 'Sample invoice #' . $i,
                'created_by' => 4,
                'posted_by' => rand(0, 10) > 3 ? 4 : null,
                'posted_at' => rand(0, 10) > 3 ? $invoiceDate : null,
            ]);

            // Add 2-5 items per invoice
            $numItems = rand(2, 5);
            $selectedProducts = array_rand(array_flip($products), $numItems);
            if (!is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }

            $subtotal = 0;
            $totalDiscount = 0;
            $totalTax = 0;
            $totalNet = 0;

            foreach ($selectedProducts as $productId) {
                $quantity = rand(1, 10);
                $unitPrice = rand(50, 1000);
                $discountPercentage = rand(0, 10);
                $taxRate = 15;

                // Tax-inclusive calculation
                $grossAmount = ($unitPrice * $quantity) * (1 - $discountPercentage / 100);
                $discountAmount = ($unitPrice * $quantity) * ($discountPercentage / 100);
                $netAmount = $grossAmount / (1 + $taxRate / 100);
                $taxAmount = $grossAmount - $netAmount;

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'description' => 'Product ' . $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => round($discountAmount, 2),
                    'gross_amount' => round($grossAmount, 2),
                    'tax_rate' => $taxRate,
                    'tax_amount' => round($taxAmount, 2),
                    'net_amount' => round($netAmount, 2),
                ]);

                $subtotal += $grossAmount;
                $totalDiscount += $discountAmount;
                $totalTax += $taxAmount;
                $totalNet += $netAmount;

                // Create stock ledger entry for posted invoices
                if ($invoice->status === 'posted') {
                    StockLedger::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'transaction_date' => $invoiceDate,
                        'reference_type' => 'sales_invoice',
                        'reference_id' => $invoice->id,
                        'reference_number' => $invoice->document_number,
                        'movement_type' => 'out',
                        'quantity' => -$quantity,
                        'unit_cost' => $unitPrice * 0.7, // Approximate cost
                        'total_cost' => round($unitPrice * 0.7 * $quantity, 2),
                        'balance_quantity' => rand(50, 200),
                        'notes' => 'Sales invoice: ' . $invoice->document_number,
                        'created_by' => 4,
                    ]);
                }
            }

            $invoice->update([
                'subtotal' => round($subtotal, 2),
                'discount_amount' => round($totalDiscount, 2),
                'tax_amount' => round($totalTax, 2),
                'total_amount' => round($totalNet, 2),
                'balance_amount' => round($totalNet, 2),
            ]);
        }
    }
}
