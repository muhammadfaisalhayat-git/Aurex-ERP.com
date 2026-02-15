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
            'warehouses',
            'local_purchases',
            'supplier_registrations',
            'customer_registrations',
            'employees',
            'departments',
            'designations',
            'audit_logs',
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'warehouses',
            'local_purchases',
            'supplier_registrations',
            'customer_registrations',
            'employees',
            'departments',
            'designations',
            'audit_logs',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'company_id')) {
                        $table->dropForeign([$tableName . '_company_id_foreign']);
                        $table->dropColumn('company_id');
                    }
                });
            }
        }
    }
};
