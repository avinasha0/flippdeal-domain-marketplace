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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']); // credit = money in, debit = money out
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('reference_id')->nullable(); // External reference (PayPal, bank, etc.)
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
