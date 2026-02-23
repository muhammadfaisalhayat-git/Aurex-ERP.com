<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tables = [
            'stock_ledger',
            'stock_balances',
            'stock_supply',
            'stock_receiving',
            'stock_transfers',
            'stock_transfer_requests',
            'stock_issue_orders',
        ];

        foreach ($tables as $tableName) {
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
        $tables = [
            'stock_ledger',
            'stock_balances',
            'stock_supply',
            'stock_receiving',
            'stock_transfers',
            'stock_transfer_requests',
            'stock_issue_orders',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'branch_id')) {
                        $table->dropForeign([$tableName . '_branch_id_foreign']);
                        $table->dropColumn('branch_id');
                    }
                });
            }
        }
    }
};
