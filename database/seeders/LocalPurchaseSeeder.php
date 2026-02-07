<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LocalPurchase;
use App\Models\LocalPurchaseItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;

class LocalPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $branches = Branch::all();
        $items = Product::all();
        $users = User::all();

        $suppliers = [
            ['name' => 'Local Hardware Store', 'phone' => '050-1234567', 'email' => 'hardware@local.com', 'address' => 'Main Street, Riyadh'],
            ['name' => 'Quick Supplies Co.', 'phone' => '050-2345678', 'email' => 'quick@supplies.com', 'address' => 'Industrial Area, Jeddah'],
            ['name' => 'City Electronics', 'phone' => '050-3456789', 'email' => 'city@electronics.com', 'address' => 'Downtown, Dammam'],
            ['name' => 'Office Depot Local', 'phone' => '050-4567890', 'email' => 'office@depot.com', 'address' => 'Business District, Riyadh'],
            ['name' => 'Al-Rashid Trading', 'phone' => '050-5678901', 'email' => 'alrashid@trading.com', 'address' => 'Old Market, Jeddah'],
            ['name' => 'Saudi Materials', 'phone' => '050-6789012', 'email' => 'materials@saudi.com', 'address' => 'Factory Road, Dammam'],
            ['name' => 'Express Parts', 'phone' => '050-7890123', 'email' => 'express@parts.com', 'address' => 'Auto Zone, Riyadh'],
            ['name' => 'General Trading LLC', 'phone' => '050-8901234', 'email' => 'general@trading.com', 'address' => 'Commercial Area, Jeddah'],
        ];

        $statuses = ['draft', 'posted'];
        $statusWeights = [20, 80]; // 20% draft, 80% posted

        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers[array_rand($suppliers)];
            $warehouse = $warehouses->random();
            $branch = $branches->random();
            $user = $users->random();

            $status = $this->weightedRandom($statuses, $statusWeights);
            $invoiceDate = Carbon::now()->subDays(rand(0, 180));

            $purchase = LocalPurchase::create([
                'document_number' => 'LP-' . date('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'invoice_number' => 'INV-' . rand(10000, 99999),
                'invoice_date' => $invoiceDate,
                'supplier_name' => $supplier['name'],
                'supplier_phone' => $supplier['phone'],
                'supplier_email' => $supplier['email'],
                'supplier_address' => $supplier['address'],
                'warehouse_id' => $warehouse->id,
                'branch_id' => $branch->id,
                'notes' => 'Local purchase from ' . $supplier['name'],
                'status' => $status,
                'created_by' => $user->id,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
            ]);

            // Add 1-5 items per purchase
            $numItems = rand(1, 5);
            $selectedItems = $items->random(min($numItems, $items->count()));

            foreach ($selectedItems as $item) {
                $quantity = rand(1, 50);
                $unitPrice = rand(10, 1000);
                $discount = rand(0, 5) == 0 ? rand(5, 50) : 0; // 20% chance of discount
                $taxRate = [0, 5, 15][array_rand([0, 5, 15])];

                $gross = $quantity * $unitPrice;
                $grossAfterDiscount = $gross - $discount;
                $net = $grossAfterDiscount / (1 + ($taxRate / 100));
                $tax = $grossAfterDiscount - $net;

                LocalPurchaseItem::create([
                    'local_purchase_id' => $purchase->id,
                    'product_id' => $item->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => round($tax, 2),
                    'total_amount' => round($grossAfterDiscount + $tax, 2),
                ]);
            }

            $purchase->calculateTotals();

            // If status is posted, post the purchase
            if ($status === 'posted') {
                $purchase->post();
            }
        }
    }

    private function weightedRandom($values, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $value;
            }
        }

        return $values[0];
    }
}
