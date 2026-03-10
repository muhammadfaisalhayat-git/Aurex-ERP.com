<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The tables to add the measurement_unit_id to.
     */
    protected $tables = [
        'sales_invoice_items',
        'purchase_invoice_items',
        'quotation_items',
        'sales_order_items',
        'sales_return_items',
        'customer_request_items',
        'supply_order_items',
        'local_purchase_items',
        'stock_transfer_items',
        'stock_transfer_request_items',
        'stock_receiving_items',
        'stock_issue_order_items',
        'production_order_components',
        'production_order_finished_goods'
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
