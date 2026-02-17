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
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('house_rent_allowance', 15, 2)->default(0)->after('basic_salary');
            $table->decimal('conveyance_allowance', 15, 2)->default(0)->after('house_rent_allowance');
            $table->decimal('dearness_allowance', 15, 2)->default(0)->after('conveyance_allowance');
            $table->decimal('overtime_allowance', 15, 2)->default(0)->after('dearness_allowance');
            $table->decimal('other_allowance', 15, 2)->default(0)->after('overtime_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'house_rent_allowance',
                'conveyance_allowance',
                'dearness_allowance',
                'overtime_allowance',
                'other_allowance',
            ]);
        });
    }
};
