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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('domain_id')->constrained('domains')->onDelete('cascade');
            $table->decimal('amount', 15, 2); // Total transaction amount
            $table->decimal('fee_amount', 15, 2)->default(0); // Platform commission fee
            $table->string('currency', 3)->default('USD');
            $table->string('provider', 50)->default('paypal'); // Payment provider
            $table->string('provider_txn_id')->nullable(); // Provider transaction ID
            $table->enum('escrow_state', ['pending', 'in_escrow', 'released', 'refunded', 'cancelled'])->default('pending');
            $table->json('escrow_metadata')->nullable(); // Additional escrow data
            $table->foreignId('escrow_release_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('escrow_released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['buyer_id', 'escrow_state']);
            $table->index(['seller_id', 'escrow_state']);
            $table->index(['domain_id', 'escrow_state']);
            $table->index('provider_txn_id');
            $table->index('escrow_state');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};