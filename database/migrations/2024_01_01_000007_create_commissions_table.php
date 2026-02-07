<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->foreignId('salesman_id')->nullable()->constrained('users');
            $table->foreignId('customer_group_id')->nullable()->constrained('customer_groups');
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories');
            $table->enum('calculation_type', ['percentage', 'fixed_amount']);
            $table->decimal('commission_value', 10, 4);
            $table->decimal('min_sales_amount', 15, 2)->nullable();
            $table->decimal('max_sales_amount', 15, 2)->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('commission_runs', function (Blueprint $table) {
            $table->id();
            $table->string('run_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'calculated', 'approved', 'locked'])->default('draft');
            $table->decimal('total_commission', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('commission_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_run_id')->constrained('commission_runs');
            $table->foreignId('salesman_id')->constrained('users');
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_returns', 15, 2)->default(0);
            $table->decimal('net_sales', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->timestamps();
        });

        Schema::create('commission_statement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_statement_id')->constrained('commission_statements')->onDelete('cascade');
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices');
            $table->decimal('invoice_amount', 15, 2);
            $table->decimal('commission_rate', 10, 4);
            $table->decimal('commission_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_statement_details');
        Schema::dropIfExists('commission_statements');
        Schema::dropIfExists('commission_runs');
        Schema::dropIfExists('commission_rules');
    }
};
