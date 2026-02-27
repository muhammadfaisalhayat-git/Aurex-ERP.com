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
        // Patients Table
        Schema::create('healthcare_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Doctors Table
        Schema::create('healthcare_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Medical Services (Products/Services catalog)
        Schema::create('healthcare_medical_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->foreignId('revenue_account_id')->nullable()->constrained('chart_of_accounts');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Appointments Table
        Schema::create('healthcare_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('healthcare_patients');
            $table->foreignId('doctor_id')->constrained('healthcare_doctors');
            $table->foreignId('service_id')->nullable()->constrained('healthcare_medical_services');
            $table->dateTime('appointment_date');
            $table->string('reference_no')->nullable();
            $table->enum('status', ['scheduled', 'arrived', 'in-progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('billing_status', ['unbilled', 'invoiced', 'paid'])->default('unbilled');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthcare_appointments');
        Schema::dropIfExists('healthcare_medical_services');
        Schema::dropIfExists('healthcare_doctors');
        Schema::dropIfExists('healthcare_patients');
    }
};
