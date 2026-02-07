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
        Schema::create('document_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type')->unique();
            $table->string('prefix');
            $table->integer('current_number')->default(0);
            $table->integer('padding')->default(5);
            $table->integer('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_numbers');
    }
};
