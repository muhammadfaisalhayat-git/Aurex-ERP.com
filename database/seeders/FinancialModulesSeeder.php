<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\Finance\AssetCategory;
use App\Models\Finance\FixedAsset;
use App\Models\Finance\Budget;
use App\Models\Finance\BudgetItem;
use App\Models\Company;
use App\Models\Branch;

class FinancialModulesSeeder extends Seeder
{
    public function run()
    {
        $company = Company::first();
        $branch = Branch::where('company_id', $company->id)->first();
        $assetType = AccountType::where('code', 'asset')->first();
        $expenseType = AccountType::where('code', 'expense')->first();

        // 1. Create COA accounts
        $nonCurrentAssets = ChartOfAccount::firstOrCreate(
        ['code' => '1200'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'Non-Current Assets',
            'name_ar' => 'أصول غير متداولة',
            'type' => 'asset',
            'account_type_id' => $assetType->id,
            'is_posting_allowed' => false,
            'is_active' => true,
        ]
        );

        $ppe = ChartOfAccount::firstOrCreate(
        ['code' => '1210'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'Property, Plant & Equipment',
            'name_ar' => 'أصول ثابتة',
            'type' => 'asset',
            'account_type_id' => $assetType->id,
            'parent_id' => $nonCurrentAssets->id,
            'is_posting_allowed' => false,
            'is_active' => true,
        ]
        );

        $vehicleAcc = ChartOfAccount::firstOrCreate(
        ['code' => '1211'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'Vehicles',
            'name_ar' => 'سيارات',
            'type' => 'asset',
            'account_type_id' => $assetType->id,
            'parent_id' => $ppe->id,
            'is_posting_allowed' => true,
            'is_active' => true,
        ]
        );

        $accumulatedDepAcc = ChartOfAccount::firstOrCreate(
        ['code' => '1213'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'Accumulated Depreciation',
            'name_ar' => 'مجمع الإهلاك',
            'type' => 'asset',
            'account_type_id' => $assetType->id,
            'parent_id' => $ppe->id,
            'is_posting_allowed' => true,
            'is_active' => true,
        ]
        );

        $adminExpenses = ChartOfAccount::firstOrCreate(
        ['code' => '6000'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'General & Administrative Expenses',
            'name_ar' => 'مصروفات عمومية وإدارية',
            'type' => 'expense',
            'account_type_id' => $expenseType->id,
            'is_posting_allowed' => false,
            'is_active' => true,
        ]
        );

        $depreciationExpAcc = ChartOfAccount::firstOrCreate(
        ['code' => '6010'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'Depreciation Expense',
            'name_ar' => 'مصروف إهلاك',
            'type' => 'expense',
            'account_type_id' => $expenseType->id,
            'parent_id' => $adminExpenses->id,
            'is_posting_allowed' => true,
            'is_active' => true,
        ]
        );

        $officeSuppliesAcc = ChartOfAccount::firstOrCreate(
        ['code' => '6020'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'name_en' => 'Office Supplies',
            'name_ar' => 'أدوات مكتبية',
            'type' => 'expense',
            'account_type_id' => $expenseType->id,
            'parent_id' => $adminExpenses->id,
            'is_posting_allowed' => true,
            'is_active' => true,
        ]
        );

        // 2. Create Asset Categories
        $vehicleCategory = AssetCategory::firstOrCreate(
        ['name_en' => 'Vehicles'],
        [
            'name_ar' => 'السيارات',
            'depreciation_method' => 'straight_line',
            'useful_life_years' => 5,
            'salvage_value_percentage' => 10,
            'asset_account_id' => $vehicleAcc->id,
            'accumulated_depreciation_account_id' => $accumulatedDepAcc->id,
            'depreciation_expense_account_id' => $depreciationExpAcc->id,
        ]
        );

        // 3. Create Fixed Assets
        FixedAsset::firstOrCreate(
        ['code' => 'AST-001'],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'asset_category_id' => $vehicleCategory->id,
            'name_en' => 'Delivery Van - Toyota',
            'name_ar' => 'سيارة توصيل - تويوتا',
            'purchase_date' => now()->subMonths(6),
            'purchase_cost' => 50000,
            'salvage_value' => 5000,
            'useful_life_years' => 5,
            'current_value' => 45500,
            'asset_account_id' => $vehicleAcc->id,
            'accumulated_depreciation_account_id' => $accumulatedDepAcc->id,
            'depreciation_expense_account_id' => $depreciationExpAcc->id,
            'status' => 'active',
        ]
        );

        // 4. Create Budget
        $budget = Budget::firstOrCreate(
        ['name' => 'Annual Operations Budget 2026', 'year' => 2026],
        [
            'company_id' => $company ? $company->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'total_amount' => 120000,
            'status' => 'approved',
        ]
        );

        BudgetItem::firstOrCreate(
        ['budget_id' => $budget->id, 'chart_of_account_id' => $officeSuppliesAcc->id],
        [
            'month_1' => 1000, 'month_2' => 1000, 'month_3' => 1000, 'month_4' => 1000,
            'month_5' => 1000, 'month_6' => 1000, 'month_7' => 1000, 'month_8' => 1000,
            'month_9' => 1000, 'month_10' => 1000, 'month_11' => 1000, 'month_12' => 1000,
            'total_amount' => 12000,
        ]
        );

        BudgetItem::firstOrCreate(
        ['budget_id' => $budget->id, 'chart_of_account_id' => $depreciationExpAcc->id],
        [
            'month_1' => 500, 'month_2' => 500, 'month_3' => 500, 'month_4' => 500,
            'month_5' => 500, 'month_6' => 500, 'month_7' => 500, 'month_8' => 500,
            'month_9' => 500, 'month_10' => 500, 'month_11' => 500, 'month_12' => 500,
            'total_amount' => 6000,
        ]
        );
    }
}
