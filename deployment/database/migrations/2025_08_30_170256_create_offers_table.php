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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('offer_amount', 10, 2);
            $table->text('message')->nullable(); // Offer message from buyer
            $table->enum('status', [
                'pending',      // Offer sent, waiting for seller response
                'accepted',     // Seller accepted the offer
                'rejected',     // Seller rejected the offer
                'expired',      // Offer expired
                'withdrawn',    // Buyer withdrew the offer
                'converted'     // Converted to order
            ])->default('pending');
            $table->timestamp('expires_at')->nullable(); // Offer expiration
            $table->timestamp('responded_at')->nullable(); // When seller responded
            $table->text('seller_response')->nullable(); // Seller's response message
            $table->json('metadata')->nullable(); // Additional offer data
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['domain_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
