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
        Schema::table('ledger_entries', function (Blueprint $row) {
            $row->string('cost_center_no')->nullable()->after('description');
            $row->string('activity_no')->nullable()->after('cost_center_no');
            $row->string('lc_no')->nullable()->after('activity_no');
            $row->string('rep')->nullable()->after('lc_no');
            $row->string('collector_no')->nullable()->after('rep');
            $row->string('promoter_code')->nullable()->after('collector_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $row) {
            $row->dropColumn([
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
