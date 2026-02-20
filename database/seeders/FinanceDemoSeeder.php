<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\PaymentVoucher;
use App\Models\ReceiptVoucher;
use App\Models\ChartOfAccount;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $accountingService = app(AccountingService::class);
        $companyId = 1; // Default company

        // 1. Create Bank & Cash Accounts
        $bankAccount = BankAccount::create([
            'company_id' => $companyId,
            'code' => 'ACC-001',
            'name_en' => 'Al Rajhi Bank - Primary',
            'account_type' => 'bank',
            'bank_name' => 'Al Rajhi Bank',
            'account_number' => '123456789012345',
            'currency_code' => 'SAR',
            'opening_balance' => 500000.00,
            'current_balance' => 500000.00,
            'chart_of_account_id' => ChartOfAccount::where('code', '101001')->first()->id ?? 1,
            'is_active' => true,
        ]);

        $cashAccount = BankAccount::create([
            'company_id' => $companyId,
            'code' => 'ACC-002',
            'name_en' => 'Main Office Petty Cash',
            'account_type' => 'cash',
            'currency_code' => 'SAR',
            'opening_balance' => 10000.00,
            'current_balance' => 10000.00,
            'chart_of_account_id' => ChartOfAccount::where('code', '101002')->first()->id ?? 2,
            'is_active' => true,
        ]);

        // 2. Fetch dependencies
        $vendors = Vendor::limit(5)->get();
        $customers = Customer::limit(5)->get();
        $expenseAccounts = ChartOfAccount::where('type', 'expense')->limit(5)->get();
        $incomeAccounts = ChartOfAccount::where('type', 'revenue')->limit(5)->get();

        // 3. Generate Random Transactions for the last 30 days
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subDays(rand(1, 30));

            // Randomly pick Payment or Receipt
            if (rand(0, 1) == 0) {
                // Payment Voucher
                $vendor = $vendors->random();
                $expenseAccount = $expenseAccounts->random();
                $amount = rand(500, 5000) + (rand(0, 99) / 100);

                $pv = PaymentVoucher::create([
                    'company_id' => $companyId,
                    'voucher_number' => 'PV-' . $date->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'voucher_date' => $date,
                    'bank_account_id' => $bankAccount->id,
                    'payee_name' => $vendor->name,
                    'vendor_id' => $vendor->id,
                    'chart_of_account_id' => $expenseAccount->id,
                    'amount' => $amount,
                    'payment_method' => ['bank_transfer', 'check', 'cash'][rand(0, 2)],
                    'description' => 'Demo payment for supplies and maintenance',
                    'status' => 'posted',
                    'posted_at' => $date,
                    'created_by' => $admin->id,
                ]);

                $accountingService->postPaymentVoucher($pv);
            }
            else {
                // Receipt Voucher
                $customer = $customers->random();
                $incomeAccount = $incomeAccounts->random();
                $amount = rand(1000, 10000) + (rand(0, 99) / 100);

                $rv = ReceiptVoucher::create([
                    'company_id' => $companyId,
                    'voucher_number' => 'RV-' . $date->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'voucher_date' => $date,
                    'bank_account_id' => $bankAccount->id,
                    'payer_name' => $customer->name,
                    'customer_id' => $customer->id,
                    'chart_of_account_id' => $incomeAccount->id,
                    'amount' => $amount,
                    'payment_method' => 'bank_transfer',
                    'description' => 'Demo receipt for services rendered',
                    'status' => 'posted',
                    'posted_at' => $date,
                    'created_by' => $admin->id,
                ]);

                $accountingService->postReceiptVoucher($rv);
            }
        }
    }
}
