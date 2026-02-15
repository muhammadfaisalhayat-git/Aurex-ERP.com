<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->enum('document_type', ['invoice', 'contract', 'certificate', 'tax_document', 'other'])->default('invoice');
            $table->string('file_path');
            $table->string('original_filename');
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index('vendor_id');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
