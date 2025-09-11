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
        Schema::table('domains', function (Blueprint $table) {
            // Bidding/Auction functionality
            $table->boolean('enable_bidding')->default(false); // Enable bidding/auction mode
            $table->decimal('starting_bid', 10, 2)->nullable(); // Starting bid amount
            $table->decimal('current_bid', 10, 2)->nullable(); // Current highest bid
            $table->integer('bid_count')->default(0); // Number of bids placed
            $table->timestamp('auction_start')->nullable(); // When auction starts
            $table->timestamp('auction_end')->nullable(); // When auction ends
            $table->enum('auction_status', ['draft', 'scheduled', 'active', 'ended', 'cancelled'])->default('draft');
            $table->decimal('reserve_price', 10, 2)->nullable(); // Minimum price to sell (reserve)
            $table->boolean('reserve_met')->default(false); // Whether reserve price has been met
            $table->integer('minimum_bid_increment')->default(10); // Minimum bid increment in dollars
            $table->boolean('auto_extend')->default(false); // Auto-extend auction if bids near end
            $table->integer('auto_extend_minutes')->default(5); // Minutes to extend if bid placed near end
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn([
                'enable_bidding', 'starting_bid', 'current_bid', 'bid_count',
                'auction_start', 'auction_end', 'auction_status', 'reserve_price',
                'reserve_met', 'minimum_bid_increment', 'auto_extend', 'auto_extend_minutes'
            ]);
        });
    }
};
