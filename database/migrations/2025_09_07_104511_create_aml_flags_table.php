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
        Schema::create('aml_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('flag_type'); // 'high_volume', 'rapid_transfers', 'email_mismatch', 'multiple_high_value'
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional data about the flag
            $table->enum('status', ['active', 'resolved', 'false_positive'])->default('active');
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('flag_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aml_flags');
    }
};