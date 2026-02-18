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
        Schema::table('journal_voucher_items', function (Blueprint $table) {
            $table->unsignedBigInteger('main_account_id')->nullable()->after('journal_voucher_id');
            $table->foreign('main_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('journal_voucher_items', function (Blueprint $table) {
            $table->dropForeign(['main_account_id']);
            $table->dropColumn('main_account_id');
        });
    }
};
