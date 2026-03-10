<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The additional tables to add the measurement_unit_id to.
     */
    protected $tables = [
        'stock_ledger',
        'stock_supply_items',
        'composite_assemblies',
        'composite_assembly_components',
        'sales_contract_items',
        'transport_order_items',
        'maintenance_voucher_parts'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'measurement_unit_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('measurement_unit_id')->nullable()->after('product_id');
                    $table->foreign('measurement_unit_id')->references('id')->on('measurement_units')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'measurement_unit_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['measurement_unit_id']);
                    $table->dropColumn('measurement_unit_id');
                });
            }
        }
    }
};
