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
        Schema::create('kyc_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->json('documents')->nullable(); // URLs to uploaded documents
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at');
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index('user_id');
            $table->index('transaction_id');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_requests');
    }
};