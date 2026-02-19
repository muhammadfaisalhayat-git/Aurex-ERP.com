<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\Branch;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class JournalVoucherModernSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first() ?? Company::create(['name' => 'Aurex ERP', 'code' => 'AUREX']);
        $branches = Branch::all();
        $admin = User::first();

        // 1. Ensure Account Types are defined and assigned
        $types = [
            ['code' => 'ASSET', 'name_en' => 'Asset', 'name_ar' => 'أصول'],
            ['code' => 'LIABILITY', 'name_en' => 'Liability', 'name_ar' => 'خصوم'],
            ['code' => 'EQUITY', 'name_en' => 'Equity', 'name_ar' => 'حقوق ملكية'],
            ['code' => 'REVENUE', 'name_en' => 'Revenue', 'name_ar' => 'إيرادات'],
            ['code' => 'EXPENSE', 'name_en' => 'Expense', 'name_ar' => 'مصروفات'],
        ];

        foreach ($types as $t) {
            AccountType::updateOrCreate(['code' => $t['code']], $t);
        }

        $allTypes = AccountType::all();
        $accounts = ChartOfAccount::where('is_posting_allowed', true)->get();

        foreach ($accounts as $index => $account) {
            if (!$account->account_type_id) {
                // Simple distribution of account types
                $type = $allTypes[$index % $allTypes->count()];
                $account->update(['account_type_id' => $type->id]);
            }
        }

        // 2. Generate 50 Journal Vouchers
        for ($i = 1; $i <= 50; $i++) {
            $branch = $branches->random();
            $amount = rand(100, 50000) / 10;

            $jv = JournalVoucher::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'voucher_number' => 'JV-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'voucher_date' => now()->subDays(rand(0, 30)),
                'doc_type' => '1-Journal',
                'description' => 'Automatic Seeder Transaction #' . $i,
                'total_amount_text' => 'Demo Amount ' . $amount,
                'status' => $i % 5 == 0 ? 'draft' : 'posted',
                'is_posted' => $i % 5 != 0,
                'created_by' => $admin->id,
            ]);

            // Pick two random accounts for debit and credit
            $debitAcc = $accounts->random();
            $creditAcc = $accounts->random();
            while ($creditAcc->id == $debitAcc->id) {
                $creditAcc = $accounts->random();
            }

            JournalVoucherItem::create([
                'journal_voucher_id' => $jv->id,
                'main_account_id' => $debitAcc->parent_id ?? $debitAcc->id,
                'chart_of_account_id' => $debitAcc->id,
                'debit' => $amount,
                'credit' => 0.00,
                'currency' => 'SR',
                'percentage' => 100,
                'notes' => 'Debit entry #' . $i,
            ]);

            JournalVoucherItem::create([
                'journal_voucher_id' => $jv->id,
                'main_account_id' => $creditAcc->parent_id ?? $creditAcc->id,
                'chart_of_account_id' => $creditAcc->id,
                'debit' => 0.00,
                'credit' => $amount,
                'currency' => 'SR',
                'percentage' => 100,
                'notes' => 'Credit entry #' . $i,
            ]);
        }
    }
}
