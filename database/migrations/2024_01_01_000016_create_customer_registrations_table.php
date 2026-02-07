<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->date('registration_date');
            $table->string('customer_name_en');
            $table->string('customer_name_ar')->nullable();
            $table->string('trade_name')->nullable();
            $table->enum('customer_type', ['individual', 'company', 'government', 'non_profit'])->default('company');
            $table->string('id_number')->nullable()->unique(); // For individuals
            $table->string('commercial_registration')->nullable()->unique(); // For companies
            $table->string('tax_number')->nullable()->unique();
            $table->string('vat_certificate_number')->nullable();
            $table->foreignId('customer_group_id')->nullable()->constrained('customer_groups');
            $table->string('contact_person');
            $table->string('contact_position')->nullable();
            $table->string('phone');
            $table->string('mobile')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('billing_address');
            $table->text('shipping_address')->nullable();
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Saudi Arabia');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('salesman_id')->nullable()->constrained('users');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('credit_days')->default(30);
            $table->enum('payment_terms', ['cash', 'credit_15', 'credit_30', 'credit_45', 'credit_60', 'credit_90'])->default('credit_30');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'blacklisted'])->default('pending');
            $table->foreignId('submitted_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('converted_to_customer_id')->nullable()->constrained('customers');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customer_registration_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_registration_id')->constrained('customer_registrations')->onDelete('cascade');
            $table->string('document_type'); // id_card, cr_certificate, tax_certificate, etc.
            $table->string('document_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_registration_documents');
        Schema::dropIfExists('customer_registrations');
    }
};
