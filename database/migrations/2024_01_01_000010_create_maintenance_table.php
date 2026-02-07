<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_workshops', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('manager_name')->nullable();
            $table->enum('workshop_type', ['internal', 'external'])->default('internal');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->date('voucher_date');
            $table->foreignId('workshop_id')->constrained('maintenance_workshops');
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->string('entity_name');
            $table->enum('maintenance_type', ['preventive', 'corrective', 'overhaul', 'inspection', 'other']);
            $table->text('problem_description');
            $table->text('work_description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->enum('status', ['open', 'in_progress', 'waiting_parts', 'completed', 'cancelled'])->default('open');
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->string('technician_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_voucher_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_voucher_id')->constrained('maintenance_vouchers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->foreignId('issued_by')->constrained('users');
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_voucher_parts');
        Schema::dropIfExists('maintenance_vouchers');
        Schema::dropIfExists('maintenance_workshops');
    }
};
