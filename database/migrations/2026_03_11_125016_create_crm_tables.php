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
        Schema::create('crm_pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('color', 20)->default('#6c757d');
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('source')->nullable(); // website, email, referral, etc.
            $table->foreignId('salesman_id')->nullable()->constrained('users');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'lost'])->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->decimal('expected_revenue', 15, 2)->default(0);
            $table->date('expected_closing')->nullable();
            $table->integer('probability')->default(0); // 0 to 100
            $table->foreignId('stage_id')->constrained('crm_pipeline_stages');
            $table->foreignId('salesman_id')->nullable()->constrained('users');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_type'); // call, meeting, follow-up, etc.
            $table->text('summary');
            $table->date('due_date');
            $table->morphs('activitable'); // To link with Lead or Opportunity
            $table->foreignId('user_id')->constrained('users'); // Assigned user
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_opportunities');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_pipeline_stages');
    }
};
