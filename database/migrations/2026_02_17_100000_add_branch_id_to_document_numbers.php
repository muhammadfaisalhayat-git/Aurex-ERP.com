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
        Schema::table('document_numbers', function (Blueprint $table) {
            // Add branch_id
            if (!Schema::hasColumn('document_numbers', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('company_id')->constrained('branches')->onDelete('cascade');
            }

            // Drop old unique index if it exists
            // SQLite might require a different approach for dropping unique constraints, 
            // but Laravel's Schema builder handles it relatively well.
            // Using try-catch or checking index existence for safety.
            try {
                $table->dropUnique('document_numbers_entity_type_unique');
            } catch (\Exception $e) {
                // Ignore if index doesn't exist by this name
            }

            // Create new composite unique index
            $table->unique(['company_id', 'branch_id', 'entity_type'], 'doc_num_branch_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_numbers', function (Blueprint $table) {
            $table->dropUnique('doc_num_branch_type_unique');
            $table->unique('entity_type', 'document_numbers_entity_type_unique');
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
