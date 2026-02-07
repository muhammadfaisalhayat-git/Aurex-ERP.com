<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('plate_number')->unique();
            $table->string('trailer_type', 50)->nullable();
            $table->decimal('capacity_kg', 10, 2)->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transport_orders', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('order_date');
            $table->foreignId('trailer_id')->constrained('trailers');
            $table->foreignId('branch_id')->constrained('branches');
            $table->string('route_from');
            $table->string('route_to');
            $table->date('scheduled_date');
            $table->date('completion_date')->nullable();
            $table->enum('status', ['draft', 'loading', 'in_transit', 'delivered', 'closed', 'cancelled'])->default('draft');
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transport_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_order_id')->constrained('transport_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transport_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->date('contract_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('contractor_name');
            $table->string('contractor_phone')->nullable();
            $table->decimal('contract_value', 15, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'terminated', 'expired'])->default('active');
            $table->text('terms_conditions')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transport_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->date('claim_date');
            $table->foreignId('transport_order_id')->constrained('transport_orders');
            $table->enum('claim_type', ['damage', 'delay', 'loss', 'other']);
            $table->text('description');
            $table->decimal('claim_amount', 15, 2)->default(0);
            $table->enum('status', ['open', 'under_review', 'approved', 'rejected', 'settled'])->default('open');
            $table->text('evidence_files')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->decimal('settled_amount', 15, 2)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('settled_by')->nullable()->constrained('users');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_claims');
        Schema::dropIfExists('transport_contracts');
        Schema::dropIfExists('transport_order_items');
        Schema::dropIfExists('transport_orders');
        Schema::dropIfExists('trailers');
    }
};
