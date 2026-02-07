<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->dateTime('transaction_date');
            $table->string('reference_type', 50);
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_number');
            $table->enum('movement_type', ['in', 'out', 'transfer_in', 'transfer_out', 'adjustment', 'assembly', 'disassembly']);
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->decimal('balance_quantity', 15, 3);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('reserved_quantity', 15, 3)->default(0);
            $table->decimal('available_quantity', 15, 3)->default(0);
            $table->decimal('average_cost', 15, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_supply', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('supply_date');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->string('reference_number')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'posted', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_supply_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_supply_id')->constrained('stock_supply')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('total_cost', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_receiving', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('receiving_date');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->string('purchase_order_number')->nullable();
            $table->string('delivery_note_number')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['pending', 'partial', 'received', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_receiving_id')->constrained('stock_receiving')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('ordered_quantity', 15, 3)->default(0);
            $table->decimal('received_quantity', 15, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('transfer_date');
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->enum('status', ['draft', 'requested', 'approved', 'in_transit', 'received', 'cancelled'])->default('draft');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('request_date');
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_transfer_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_request_id')->constrained('stock_transfer_requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_issue_orders', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('issue_date');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('issue_type', ['sales', 'transfer', 'consumption', 'damage', 'other']);
            $table->enum('status', ['draft', 'confirmed', 'posted', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_issue_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_issue_order_id')->constrained('stock_issue_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('composite_assemblies', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->date('assembly_date');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 15, 3);
            $table->decimal('cost_per_unit', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('composite_assembly_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('composite_assembly_id')->constrained('composite_assemblies')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('products');
            $table->decimal('quantity_used', 15, 3);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('composite_assembly_components');
        Schema::dropIfExists('composite_assemblies');
        Schema::dropIfExists('stock_issue_order_items');
        Schema::dropIfExists('stock_issue_orders');
        Schema::dropIfExists('stock_transfer_request_items');
        Schema::dropIfExists('stock_transfer_requests');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_receiving_items');
        Schema::dropIfExists('stock_receiving');
        Schema::dropIfExists('stock_supply_items');
        Schema::dropIfExists('stock_supply');
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('stock_ledger');
    }
};
