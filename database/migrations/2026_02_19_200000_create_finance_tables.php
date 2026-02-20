<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban', 34)->nullable();
            $table->string('currency_code', 3)->default('SAR');
            $table->enum('account_type', ['bank', 'cash'])->default('bank');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('voucher_number')->unique();
            $table->date('voucher_date');
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->string('payee_name');
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, check, cash
            $table->string('reference_number')->nullable(); // check no, transfer ref
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'posted', 'voided'])->default('draft');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts'); // for direct expense posting
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('voucher_number')->unique();
            $table->date('voucher_date');
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->string('payer_name');
            $table->string('payment_method')->default('bank_transfer');
            $table->string('reference_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'posted', 'voided'])->default('draft');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts'); // for direct income posting
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_vouchers');
        Schema::dropIfExists('payment_vouchers');
        Schema::dropIfExists('bank_accounts');
    }
};
