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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('domain_id')->nullable()->constrained('domains')->onDelete('cascade');
            $table->string('path'); // Storage path
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size'); // File size in bytes
            $table->enum('scan_status', ['pending', 'clean', 'infected', 'error'])->default('pending');
            $table->json('scan_report')->nullable(); // Scan results and details
            $table->string('storage_disk')->default('s3');
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index(['domain_id', 'created_at']);
            $table->index('scan_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};