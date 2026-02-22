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
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->enum('return_type', ['cash', 'credit'])->default('credit')->after('sales_invoice_id');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->after('return_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn(['return_type', 'bank_account_id']);
        });
    }
};
