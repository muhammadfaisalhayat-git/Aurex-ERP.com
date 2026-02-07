<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommissionRun;
use App\Models\CommissionStatement;
use Carbon\Carbon;

class CommissionRunSeeder extends Seeder
{
    public function run(): void
    {
        $run = CommissionRun::create([
            'run_number' => 'CMR-00001',
            'start_date' => Carbon::now()->subMonth()->startOfMonth(),
            'end_date' => Carbon::now()->subMonth()->endOfMonth(),
            'status' => 'approved',
            'total_commission' => 12500.00,
            'created_by' => 4,
            'approved_by' => 1,
            'approved_at' => Carbon::now()->subDays(5),
            'notes' => 'Monthly commission calculation for ' . Carbon::now()->subMonth()->format('F Y'),
        ]);

        // Create commission statements for salesmen
        $salesmen = [5];
        foreach ($salesmen as $salesmanId) {
            CommissionStatement::create([
                'commission_run_id' => $run->id,
                'salesman_id' => $salesmanId,
                'total_sales' => 150000.00,
                'total_returns' => 5000.00,
                'net_sales' => 145000.00,
                'commission_amount' => 7250.00,
                'status' => 'approved',
            ]);
        }
    }
}
