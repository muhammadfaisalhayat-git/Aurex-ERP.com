<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('healthcare_appointments', function (Blueprint $table) {
            $table->foreignId('journal_voucher_id')->nullable()->after('billing_status')->constrained('journal_vouchers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('healthcare_appointments', function (Blueprint $table) {
            $table->dropForeign(['journal_voucher_id']);
            $table->dropColumn('journal_voucher_id');
        });
    }
};
