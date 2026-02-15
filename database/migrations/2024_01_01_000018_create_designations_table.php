<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $col) {
            $col->id();
            $col->string('name_en');
            $col->string('name_ar')->nullable();
            $col->string('code')->unique();
            $col->foreignId('department_id')->constrained()->onDelete('cascade');
            $col->text('description')->nullable();
            $col->boolean('is_active')->default(true);
            $col->timestamps();
            $col->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
