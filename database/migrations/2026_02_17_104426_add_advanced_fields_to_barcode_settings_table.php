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
            $table->string('barcode_color')->default('#000000')->after('font_size_barcode');
            $table->string('text_color')->default('#000000')->after('barcode_color');
            $table->string('content_alignment')->default('center')->after('text_color');
            $table->boolean('check_digit')->default(false)->after('content_alignment');
            $table->boolean('ucc_ean_128')->default(false)->after('check_digit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_settings', function (Blueprint $table) {
            $table->dropColumn([
                'barcode_color',
                'text_color',
                'content_alignment',
                'check_digit',
                'ucc_ean_128'
            ]);
        });
    }
};
