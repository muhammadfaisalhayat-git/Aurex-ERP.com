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
        Schema::table('product_bom', function (Blueprint $table) {
            $table->foreignId('measurement_unit_id')->nullable()->after('component_id')->constrained('measurement_units')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_bom', function (Blueprint $table) {
            $table->dropForeign(['measurement_unit_id']);
            $table->dropColumn('measurement_unit_id');
        });
    }
};
