<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $col) {
            $col->id();
            $col->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $col->string('employee_code')->unique();
            $col->string('first_name_en');
            $col->string('last_name_en');
            $col->string('first_name_ar')->nullable();
            $col->string('last_name_ar')->nullable();
            $col->string('email')->unique();
            $col->string('phone')->nullable();
            $col->date('date_of_birth')->nullable();
            $col->enum('gender', ['male', 'female', 'other'])->nullable();
            $col->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $col->foreignId('designation_id')->nullable()->constrained()->onDelete('set null');
            $col->date('joining_date');
            $col->date('exit_date')->nullable();
            $col->decimal('basic_salary', 15, 2)->default(0);
            $col->string('national_id')->nullable();
            $col->string('passport_number')->nullable();
            $col->string('iban')->nullable();
            $col->enum('status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
            $col->timestamps();
            $col->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
