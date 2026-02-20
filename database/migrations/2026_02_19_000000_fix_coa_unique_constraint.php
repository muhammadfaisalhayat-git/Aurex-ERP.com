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
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            // Drop the existing unique constraint on 'code'
            // In SQLite, dropping indexes is straightforward, but for generic compatibility:
            $table->dropUnique(['code']);

            // Add a composite unique constraint for multi-tenancy
            $table->unique(['code', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropUnique(['code', 'company_id']);
            $table->unique(['code']);
        });
    }
};
