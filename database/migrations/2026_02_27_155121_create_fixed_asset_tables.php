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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('asset_category_id')->constrained();
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 20, 2);
            $table->decimal('salvage_value', 20, 2)->default(0);
            $table->integer('useful_life_years');
            $table->string('depreciation_method')->default('straight_line');
            $table->foreignId('asset_account_id')->constrained('chart_of_accounts');
            $table->foreignId('accumulated_depreciation_account_id')->constrained('chart_of_accounts');
            $table->foreignId('depreciation_expense_account_id')->constrained('chart_of_accounts');
            $table->decimal('current_value', 20, 2);
            $table->string('status')->default('active'); // active, disposed, fully_depreciated
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
