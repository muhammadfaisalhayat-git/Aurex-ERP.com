<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('tax_enabled')->default(true);
            $table->decimal('default_tax_rate', 5, 2)->default(15.00);
            $table->enum('rounding_mode', ['per_line', 'per_invoice'])->default('per_line');
            $table->string('tax_name_en')->default('VAT');
            $table->string('tax_name_ar')->default('ضريبة القيمة المضافة');
            $table->string('tax_number')->nullable();
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('symbol');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50)->unique();
            $table->string('prefix', 10)->default('');
            $table->unsignedInteger('current_number')->default(0);
            $table->unsignedInteger('padding')->default(5);
            $table->unsignedInteger('year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('tax_settings');
    }
};
