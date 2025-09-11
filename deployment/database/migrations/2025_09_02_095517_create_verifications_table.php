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
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['paypal_email', 'government_id', 'phone', 'domain_ownership']);
            $table->enum('status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->json('verification_data')->nullable(); // Store verification-specific data
            $table->string('verification_code')->nullable(); // For email/phone verification
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who verified
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type', 'status']);
            $table->index(['verification_code']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};