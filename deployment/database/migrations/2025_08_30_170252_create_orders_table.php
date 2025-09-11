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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Unique order identifier
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('domain_price', 10, 2); // Original domain price
            $table->decimal('commission_amount', 10, 2); // Marketplace commission
            $table->decimal('total_amount', 10, 2); // Total amount buyer pays
            $table->decimal('seller_amount', 10, 2); // Amount seller receives
            $table->enum('status', [
                'pending',      // Order created, waiting for payment
                'paid',         // Payment received, escrow active
                'in_escrow',    // Funds held in escrow
                'completed',    // Domain transferred, funds released
                'cancelled',    // Order cancelled
                'disputed',     // Dispute raised
                'refunded'      // Order refunded
            ])->default('pending');
            $table->enum('payment_method', ['stripe', 'paypal', 'razorpay'])->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('escrow_released_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional order data
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['domain_id', 'status']);
            $table->index(['order_number']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
