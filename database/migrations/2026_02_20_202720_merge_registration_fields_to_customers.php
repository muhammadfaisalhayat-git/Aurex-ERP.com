<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // New fields for registration
            $table->string('registration_number')->nullable()->unique()->after('code');
            $table->date('registration_date')->nullable()->after('registration_number');
            $table->string('trade_name')->nullable()->after('name_ar');
            $table->string('id_number')->nullable()->unique()->after('customer_type');
            $table->string('vat_certificate_number')->nullable()->after('tax_number');
            $table->string('contact_position')->nullable()->after('contact_person');
            $table->string('website')->nullable()->after('email');
            $table->string('country')->default('Saudi Arabia')->after('postal_code');
            $table->text('shipping_address')->nullable()->after('address');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->after('notes');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->after('submitted_by');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');
            $table->string('business_type')->nullable()->after('rejection_reason');

            // Modifications
            $table->string('name_ar')->nullable()->change();
            // Status update (Laravel 11 handles this nicely for SQLite)
            $table->string('status')->default('pending')->change();
        });

        // Update documents table to link to customers
        if (Schema::hasTable('customer_registration_documents')) {
            Schema::table('customer_registration_documents', function (Blueprint $table) {
                $table->foreignId('customer_id')->nullable()->after('customer_registration_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('customer_registration_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer_registration_documents')) {
            Schema::table('customer_registration_documents', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
                $table->foreignId('customer_registration_id')->nullable(false)->change();
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'registration_number', 'registration_date', 'trade_name', 'id_number',
                'vat_certificate_number', 'contact_position', 'website', 'country',
                'shipping_address', 'submitted_by', 'reviewed_by', 'reviewed_at',
                'rejection_reason', 'business_type'
            ]);
            $table->string('name_ar')->nullable(false)->change();
            $table->string('status')->default('active')->change();
        });
    }
};
