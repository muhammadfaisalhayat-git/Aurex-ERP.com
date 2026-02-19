<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\User;

class JournalVoucherSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // Fetch some accounts
        $cashAcc = ChartOfAccount::where('code', '1101')->first();
        $bankAcc = ChartOfAccount::where('code', '1102')->first();
        $receivableAcc = ChartOfAccount::where('code', '1103')->first();
        $payableAcc = ChartOfAccount::where('code', '2001')->first();
        $revenueAcc = ChartOfAccount::where('code', '4000')->first();

        $customer = Customer::first();
        $vendor = Vendor::first();

        // 1. A Posted Sales Voucher
        $jv1 = JournalVoucher::create([
            'company_id' => 1,
            'branch_id' => 1,
            'voucher_number' => 'JV-2026-0001',
            'voucher_date' => now()->subDays(5),
            'doc_type' => '1-Journal',
            'description' => 'Sales Revenue Recognition - Demo',
            'total_amount_text' => 'One Thousand Riyals Only',
            'status' => 'posted',
            'is_posted' => true,
            'created_by' => $admin->id,
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv1->id,
            'main_account_id' => $receivableAcc->parent_id ?? $receivableAcc->id,
            'chart_of_account_id' => $receivableAcc->id,
            'customer_id' => $customer?->id,
            'debit' => 1000.00,
            'credit' => 0.00,
            'currency' => 'SR',
            'percentage' => 100,
            'notes' => 'Sales to ' . ($customer?->name_en ?? 'Retail Customer'),
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv1->id,
            'main_account_id' => $revenueAcc->parent_id ?? $revenueAcc->id,
            'chart_of_account_id' => $revenueAcc->id,
            'debit' => 0.00,
            'credit' => 1000.00,
            'currency' => 'SR',
            'percentage' => 100,
            'notes' => 'Revenue credited',
        ]);

        // 2. A Draft Expense Voucher
        $jv2 = JournalVoucher::create([
            'company_id' => 1,
            'branch_id' => 1,
            'voucher_number' => 'JV-2026-0002',
            'voucher_date' => now(),
            'doc_type' => '1-Journal',
            'description' => 'Vendor Payment Pending - Demo',
            'total_amount_text' => 'Five Hundred Riyals Only',
            'status' => 'draft',
            'is_posted' => false,
            'created_by' => $admin->id,
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv2->id,
            'main_account_id' => $payableAcc->parent_id ?? $payableAcc->id,
            'chart_of_account_id' => $payableAcc->id,
            'vendor_id' => $vendor?->id,
            'debit' => 500.00,
            'credit' => 0.00,
            'currency' => 'SR',
            'percentage' => 100,
            'notes' => 'Settlement for ' . ($vendor?->name_en ?? 'Main Supplier'),
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv2->id,
            'main_account_id' => $cashAcc->parent_id ?? $cashAcc->id,
            'chart_of_account_id' => $cashAcc->id,
            'debit' => 0.00,
            'credit' => 500.00,
            'currency' => 'SR',
            'percentage' => 100,
            'notes' => 'Cash out',
        ]);

        // 3. A Multi-line Complex Voucher
        $jv3 = JournalVoucher::create([
            'company_id' => 1,
            'branch_id' => 1,
            'voucher_number' => 'JV-2026-0003',
            'voucher_date' => now(),
            'doc_type' => '1-Journal',
            'description' => 'Multi-center Allocation - Demo',
            'total_amount_text' => 'Two Thousand Five Hundred Riyals Only',
            'status' => 'draft',
            'is_posted' => false,
            'created_by' => $admin->id,
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv3->id,
            'main_account_id' => $bankAcc->parent_id ?? $bankAcc->id,
            'chart_of_account_id' => $bankAcc->id,
            'debit' => 2500.00,
            'credit' => 0.00,
            'currency' => 'SR',
            'percentage' => 100,
            'notes' => 'Bank deposit',
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv3->id,
            'main_account_id' => $revenueAcc->parent_id ?? $revenueAcc->id,
            'chart_of_account_id' => $revenueAcc->id,
            'debit' => 0.00,
            'credit' => 1500.00,
            'cost_center_no' => 'CC-01',
            'activity_no' => 'ACT-01',
            'currency' => 'SR',
            'percentage' => 60,
            'notes' => 'Revenue Part A',
        ]);

        JournalVoucherItem::create([
            'journal_voucher_id' => $jv3->id,
            'main_account_id' => $revenueAcc->parent_id ?? $revenueAcc->id,
            'chart_of_account_id' => $revenueAcc->id,
            'debit' => 0.00,
            'credit' => 1000.00,
            'cost_center_no' => 'CC-02',
            'activity_no' => 'ACT-02',
            'currency' => 'SR',
            'percentage' => 40,
            'notes' => 'Revenue Part B',
        ]);
    }
}
