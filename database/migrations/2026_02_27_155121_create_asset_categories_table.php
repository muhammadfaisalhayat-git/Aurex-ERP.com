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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('depreciation_method')->default('straight_line'); // straight_line, declining_balance
            $table->integer('useful_life_years');
            $table->decimal('salvage_value_percentage', 5, 2)->default(0);
            $table->foreignId('asset_account_id')->constrained('chart_of_accounts');
            $table->foreignId('accumulated_depreciation_account_id')->constrained('chart_of_accounts');
            $table->foreignId('depreciation_expense_account_id')->constrained('chart_of_accounts');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
