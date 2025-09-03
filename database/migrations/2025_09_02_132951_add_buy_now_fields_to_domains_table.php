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
            // Buy Now functionality
            $table->boolean('enable_buy_now')->default(false); // Enable buy now option
            $table->decimal('buy_now_price', 10, 2)->nullable(); // Buy now price
            $table->boolean('buy_now_available')->default(true); // Whether buy now is currently available
            $table->timestamp('buy_now_expires_at')->nullable(); // Optional expiration for buy now
            
            // Make An Offer functionality
            $table->boolean('enable_offers')->default(false); // Enable make an offer option
            $table->decimal('minimum_offer', 10, 2)->nullable(); // Minimum offer amount
            $table->decimal('maximum_offer', 10, 2)->nullable(); // Maximum offer amount (optional)
            $table->boolean('auto_accept_offers')->default(false); // Auto-accept offers above certain amount
            $table->decimal('auto_accept_threshold', 10, 2)->nullable(); // Amount above which offers are auto-accepted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn([
                'enable_buy_now', 'buy_now_price', 'buy_now_available', 'buy_now_expires_at',
                'enable_offers', 'minimum_offer', 'maximum_offer', 'auto_accept_offers', 'auto_accept_threshold'
            ]);
        });
    }
};