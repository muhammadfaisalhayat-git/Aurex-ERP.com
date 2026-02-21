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
        Schema::create('account_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->string('module'); // e.g., 'inventory', 'payroll'
            $table->string('key'); // e.g., 'stock_receiving', 'basic_salary'
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_mappings');
    }
};
