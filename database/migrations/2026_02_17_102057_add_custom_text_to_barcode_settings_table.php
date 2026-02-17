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
            $table->string('custom_text')->nullable()->after('show_product_price');
            $table->boolean('show_custom_text')->default(false)->after('custom_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_settings', function (Blueprint $table) {
            $table->dropColumn(['custom_text', 'show_custom_text']);
        });
    }
};
