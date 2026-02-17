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
            $table->integer('font_size_name')->default(10)->after('template');
            $table->integer('font_size_code')->default(8)->after('font_size_name');
            $table->integer('font_size_price')->default(12)->after('font_size_code');
            $table->integer('font_size_custom')->default(8)->after('font_size_price');
            $table->integer('font_size_barcode')->default(40)->after('font_size_custom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_settings', function (Blueprint $table) {
            $table->dropColumn([
                'font_size_name',
                'font_size_code',
                'font_size_price',
                'font_size_custom',
                'font_size_barcode'
            ]);
        });
    }
};
