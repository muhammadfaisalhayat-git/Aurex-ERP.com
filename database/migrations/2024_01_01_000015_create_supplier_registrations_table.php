<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->date('registration_date');
            $table->string('company_name_en');
            $table->string('company_name_ar')->nullable();
            $table->string('trade_name')->nullable();
            $table->enum('company_type', ['individual', 'partnership', 'llc', 'joint_stock', 'foreign'])->default('llc');
            $table->string('commercial_registration')->unique();
            $table->date('cr_issue_date');
            $table->date('cr_expiry_date');
            $table->string('tax_number')->nullable()->unique();
            $table->string('vat_certificate_number')->nullable();
            $table->string('contact_person');
            $table->string('contact_position')->nullable();
            $table->string('phone');
            $table->string('mobile')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Saudi Arabia');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('iban')->nullable();
            $table->text('business_activities');
            $table->json('product_categories')->nullable();
            $table->enum('payment_terms', ['cash', 'credit_15', 'credit_30', 'credit_45', 'credit_60', 'credit_90'])->default('credit_30');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'blacklisted'])->default('pending');
            $table->foreignId('submitted_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('converted_to_vendor_id')->nullable()->constrained('vendors');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier_registration_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_registration_id')->constrained('supplier_registrations')->onDelete('cascade');
            $table->string('document_type'); // cr_certificate, tax_certificate, bank_statement, etc.
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
        Schema::dropIfExists('supplier_registration_documents');
        Schema::dropIfExists('supplier_registrations');
    }
};
