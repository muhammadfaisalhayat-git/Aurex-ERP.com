<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'product_categories',
            'products',
            'customer_groups',
            'customers',
            'vendors',
            'stock_ledger',
            'stock_balances',
            'stock_supply',
            'stock_receiving',
            'stock_transfers',
            'stock_transfer_requests',
            'stock_issue_orders',
            'composite_assemblies',
            'customer_requests',
            'quotations',
            'sales_contracts',
            'sales_invoices',
            'sales_returns',
            'purchase_invoices',
            'trailers',
            'transport_orders',
            'transport_contracts',
            'transport_claims',
            'maintenance_workshops',
            'maintenance_vouchers',
            'commission_rules',
            'commission_runs',
            'commission_statements',
            'system_settings',
            'tax_settings',
            'document_numbers'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'company_id')) {
                        $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
                    }
                });
            }
        }

        // Specific tables that need branch_id but might not have it
        $needsBranch = ['products', 'product_categories', 'system_settings', 'tax_settings'];
        foreach ($needsBranch as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'branch_id')) {
                        $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // Down migration logic for safety (if needed)
    }
};
