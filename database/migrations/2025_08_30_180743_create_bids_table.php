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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->foreignId('bidder_id')->constrained('users')->onDelete('cascade');
            $table->decimal('bid_amount', 10, 2);
            $table->enum('status', ['active', 'outbid', 'won', 'cancelled'])->default('active');
            $table->timestamp('bid_at');
            $table->timestamp('outbid_at')->nullable(); // When this bid was outbid
            $table->boolean('is_auto_bid')->default(false); // Whether this was an auto-bid
            $table->decimal('max_auto_bid', 10, 2)->nullable(); // Maximum auto-bid amount
            $table->text('bidder_note')->nullable(); // Optional note from bidder
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['domain_id', 'status']);
            $table->index(['bidder_id', 'status']);
            $table->index(['domain_id', 'bid_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
