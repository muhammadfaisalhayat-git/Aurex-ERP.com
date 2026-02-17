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
        Schema::table('barcode_settings', function (Blueprint $table) {
            $table->decimal('pos_x_name', 8, 2)->default(0);
            $table->decimal('pos_y_name', 8, 2)->default(0);
            $table->decimal('pos_x_code', 8, 2)->default(0);
            $table->decimal('pos_y_code', 8, 2)->default(0);
            $table->decimal('pos_x_price', 8, 2)->default(0);
            $table->decimal('pos_y_price', 8, 2)->default(0);
            $table->decimal('pos_x_custom', 8, 2)->default(0);
            $table->decimal('pos_y_custom', 8, 2)->default(0);
            $table->decimal('pos_x_barcode', 8, 2)->default(0);
            $table->decimal('pos_y_barcode', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pos_x_name',
                'pos_y_name',
                'pos_x_code',
                'pos_y_code',
                'pos_x_price',
                'pos_y_price',
                'pos_x_custom',
                'pos_y_custom',
                'pos_x_barcode',
                'pos_y_barcode'
            ]);
        });
    }
};
