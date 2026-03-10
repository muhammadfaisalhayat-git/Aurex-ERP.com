<?php

namespace App\Services;

use App\Models\StockLedger;
use App\Models\StockBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StockManagementService
{
    /**
     * Record a stock movement and update balances.
     */
    public function recordMovement(array $data)
    {
        return DB::transaction(function () use ($data) {
            $companyId = $data['company_id'] ?? Session::get('active_company_id');
            $branchId = $data['branch_id'] ?? Session::get('active_branch_id');

            // 1. Get/Update Stock Balance
            $balance = StockBalance::firstOrNew([
                'company_id' => $companyId,
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
            ]);

            $qtyChange = ($data['movement_type'] === 'in') ? $data['quantity'] : -$data['quantity'];

            // Average Cost Calculation logic (Simplified)
            if ($data['movement_type'] === 'in' && isset($data['unit_cost'])) {
                $currentTotalValue = $balance->quantity * $balance->average_cost;
                $newIncomingValue = $data['quantity'] * $data['unit_cost'];
                $newTotalQty = $balance->quantity + $data['quantity'];

                if ($newTotalQty > 0) {
                    $balance->average_cost = ($currentTotalValue + $newIncomingValue) / $newTotalQty;
                }
            }

            $balance->quantity += $qtyChange;
            $balance->available_quantity = $balance->quantity - $balance->reserved_quantity;
            $balance->save();

            // 2. Record in Ledger
            $ledger = StockLedger::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'product_id' => $data['product_id'],
                'measurement_unit_id' => $data['measurement_unit_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'transaction_date' => $data['transaction_date'] ?? now(),
                'reference_type' => $data['reference_type'],
                'reference_id' => $data['reference_id'],
                'reference_number' => $data['reference_number'],
                'movement_type' => $data['movement_type'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] ?? 0,
                'total_cost' => ($data['unit_cost'] ?? 0) * $data['quantity'],
                'balance_quantity' => $balance->quantity,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            return $ledger;
        });
    }

    /**
     * Reverse a stock movement.
     */
    public function reverseMovement($referenceType, $referenceId)
    {
        return DB::transaction(function () use ($referenceType, $referenceId) {
            $ledgerEntries = StockLedger::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->get();

            foreach ($ledgerEntries as $ledger) {
                $balance = StockBalance::where('company_id', $ledger->company_id)
                    ->where('product_id', $ledger->product_id)
                    ->where('warehouse_id', $ledger->warehouse_id)
                    ->first();

                if ($balance) {
                    $qtyChange = ($ledger->movement_type === 'in') ? -$ledger->quantity : $ledger->quantity;
                    $balance->quantity += $qtyChange;
                    $balance->available_quantity = $balance->quantity - $balance->reserved_quantity;
                    $balance->save();
                }

                $ledger->delete();
            }

            return true;
        });
    }
}
