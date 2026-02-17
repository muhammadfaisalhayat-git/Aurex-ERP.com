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
        Schema::create('barcode_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');

            $table->string('barcode_type')->default('C128');
            $table->string('page_size')->default('A4'); // A4, Label, Custom
            $table->decimal('label_width', 8, 2)->default(50.00); // mm
            $table->decimal('label_height', 8, 2)->default(30.00); // mm
            $table->integer('labels_per_row')->default(3);

            $table->decimal('margin_top', 8, 2)->default(5.00); // mm
            $table->decimal('margin_bottom', 8, 2)->default(5.00);
            $table->decimal('margin_left', 8, 2)->default(5.00);
            $table->decimal('margin_right', 8, 2)->default(5.00);

            $table->boolean('show_product_name')->default(true);
            $table->boolean('show_product_code')->default(true);
            $table->boolean('show_product_price')->default(true);

            $table->string('template')->default('default'); // default, modern, compact

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_settings');
    }
};
