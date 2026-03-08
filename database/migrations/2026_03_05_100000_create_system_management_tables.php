<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // User Login Details
        Schema::create('user_login_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20)->default('success'); // success, failed
            $table->timestamps();
            $table->index('user_id');
            $table->index('login_at');
        });

        // User Groups
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User Group pivot
        Schema::create('user_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_group_id', 'user_id']);
        });

        // User Header Settings
        Schema::create('user_header_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('header_title')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('show_company')->default(true);
            $table->boolean('show_branch')->default(true);
            $table->boolean('show_date')->default(true);
            $table->timestamps();
            $table->unique('user_id');
        });

        // Favorite Screens
        Schema::create('favorite_screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('route_name');
            $table->string('label');
            $table->string('icon')->default('fas fa-star');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'route_name']);
        });

        // Mandatory Field Configs
        Schema::create('mandatory_field_configs', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('field_name');
            $table->string('field_label');
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['module', 'field_name']);
        });

        // Alert Rules
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module');
            $table->string('condition_type'); // low_stock, overdue_invoice, budget_exceeded, etc.
            $table->decimal('threshold', 15, 2)->default(0);
            $table->json('recipients')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Backup Logs
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('disk')->default('local');
            $table->string('status')->default('completed'); // completed, failed, in_progress
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // User Signatures
        Schema::create('user_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('signature_data'); // base64 encoded
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Archived Documents
        Schema::create('archived_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // invoice, quotation, purchase_order, etc.
            $table->unsignedBigInteger('document_id');
            $table->string('original_number');
            $table->string('file_path')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['document_type', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_documents');
        Schema::dropIfExists('user_signatures');
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('alert_rules');
        Schema::dropIfExists('mandatory_field_configs');
        Schema::dropIfExists('favorite_screens');
        Schema::dropIfExists('user_header_settings');
        Schema::dropIfExists('user_group_user');
        Schema::dropIfExists('user_groups');
        Schema::dropIfExists('user_login_details');
    }
};
