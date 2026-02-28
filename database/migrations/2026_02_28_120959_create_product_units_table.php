<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measurement_unit_id')->constrained('measurement_units')->cascadeOnDelete();
            $table->decimal('package', 10, 4)->default(1);
            $table->decimal('price', 15, 4)->default(0);
            $table->string('description')->nullable();
            $table->string('foreign_description')->nullable();
            $table->string('barcode')->nullable();

            // Operational Flags
            $table->boolean('is_purchase_unit')->default(false);
            $table->boolean('is_transfer_unit')->default(false);
            $table->boolean('is_stocktaking_unit')->default(false);
            $table->boolean('is_not_for_sale')->default(false);
            $table->boolean('is_inactive')->default(false);
            $table->boolean('is_production_unit')->default(false);
            $table->boolean('is_store_unit')->default(false);
            $table->boolean('is_customer_self_service')->default(false);
            $table->boolean('excluded_from_discount')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
