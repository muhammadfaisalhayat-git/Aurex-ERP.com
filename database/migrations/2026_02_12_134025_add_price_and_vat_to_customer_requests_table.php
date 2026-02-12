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
        Schema::table('customer_requests', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('branch_id');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal');
            $table->decimal('total_amount', 15, 2)->default(0)->after('tax_amount');
        });

        Schema::table('customer_request_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 4)->default(0)->after('quantity');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
            $table->decimal('total_amount', 15, 2)->default(0)->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_request_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'tax_rate', 'tax_amount', 'total_amount']);
        });

        Schema::table('customer_requests', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_amount', 'total_amount']);
        });
    }
};
