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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // External payment provider transaction ID
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'payment',      // Payment received
                'refund',       // Refund issued
                'chargeback',   // Chargeback initiated
                'commission',   // Commission deduction
                'escrow',       // Escrow transaction
                'release'       // Escrow release
            ]);
            $table->enum('status', [
                'pending',      // Transaction initiated
                'processing',   // Transaction being processed
                'completed',    // Transaction successful
                'failed',       // Transaction failed
                'cancelled',    // Transaction cancelled
                'disputed'      // Transaction disputed
            ])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', ['stripe', 'paypal', 'razorpay', 'escrow'])->nullable();
            $table->json('payment_details')->nullable(); // Payment provider response
            $table->text('description')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['order_id', 'type']);
            $table->index(['user_id', 'type']);
            $table->index(['status', 'created_at']);
            $table->index(['transaction_id']);
            $table->index(['payment_method', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
