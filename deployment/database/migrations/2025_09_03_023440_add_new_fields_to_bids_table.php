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
        Schema::table('bids', function (Blueprint $table) {
            // Add new fields if they don't exist
            if (!Schema::hasColumn('bids', 'is_winning')) {
                $table->boolean('is_winning')->default(false);
            }
            if (!Schema::hasColumn('bids', 'is_outbid')) {
                $table->boolean('is_outbid')->default(false);
            }
            
            // Rename bidder_id to user_id if needed
            if (Schema::hasColumn('bids', 'bidder_id') && !Schema::hasColumn('bids', 'user_id')) {
                $table->renameColumn('bidder_id', 'user_id');
            }
            
            // Rename bid_amount to amount if needed
            if (Schema::hasColumn('bids', 'bid_amount') && !Schema::hasColumn('bids', 'amount')) {
                $table->renameColumn('bid_amount', 'amount');
            }
            
            // Add bid_at timestamp if it doesn't exist
            if (!Schema::hasColumn('bids', 'bid_at')) {
                $table->timestamp('bid_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            if (Schema::hasColumn('bids', 'is_winning')) {
                $table->dropColumn('is_winning');
            }
            if (Schema::hasColumn('bids', 'is_outbid')) {
                $table->dropColumn('is_outbid');
            }
            if (Schema::hasColumn('bids', 'bid_at')) {
                $table->dropColumn('bid_at');
            }
            
            // Rename back if needed
            if (Schema::hasColumn('bids', 'user_id') && !Schema::hasColumn('bids', 'bidder_id')) {
                $table->renameColumn('user_id', 'bidder_id');
            }
            if (Schema::hasColumn('bids', 'amount') && !Schema::hasColumn('bids', 'bid_amount')) {
                $table->renameColumn('amount', 'bid_amount');
            }
        });
    }
};