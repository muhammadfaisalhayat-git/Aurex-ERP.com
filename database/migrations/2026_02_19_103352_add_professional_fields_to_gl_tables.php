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
        Schema::table('journal_vouchers', function (Blueprint $table) {
            $table->string('doc_type')->default('1-Journal')->after('id');
            $table->integer('no_of_attachments')->default(0)->after('reference_no');
            $table->string('recipient_name')->nullable()->after('no_of_attachments');
            $table->string('beneficiary_name')->nullable()->after('recipient_name');
            $table->string('total_amount_text')->nullable()->after('description');
            $table->string('branch_name')->nullable()->after('branch_id');
            $table->boolean('is_posted')->default(false)->after('status');
            $table->boolean('is_reversed')->default(false)->after('is_posted');
            $table->boolean('is_periodic')->default(false)->after('is_reversed');
            $table->boolean('is_currency_discrepancy')->default(false)->after('is_periodic');
            $table->boolean('is_suspended')->default(false)->after('is_currency_discrepancy');
        });

        Schema::table('journal_voucher_items', function (Blueprint $table) {
            $table->string('currency')->default('SR')->after('chart_of_account_id');
            $table->decimal('percentage', 5, 2)->nullable()->after('currency');
            $table->string('cost_center_no')->nullable()->after('credit');
            $table->string('activity_no')->nullable()->after('cost_center_no');
            $table->string('lc_no')->nullable()->after('activity_no');
            $table->string('rep')->nullable()->after('lc_no');
            $table->string('collector_no')->nullable()->after('rep');
            $table->string('promoter_code')->nullable()->after('collector_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'doc_type',
                'no_of_attachments',
                'recipient_name',
                'beneficiary_name',
                'total_amount_text',
                'branch_name',
                'is_posted',
                'is_reversed',
                'is_periodic',
                'is_currency_discrepancy',
                'is_suspended'
            ]);
        });

        Schema::table('journal_voucher_items', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'percentage',
                'cost_center_no',
                'activity_no',
                'lc_no',
                'rep',
                'collector_no',
                'promoter_code'
            ]);
        });
    }
};
