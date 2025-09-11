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
            // Buy It Now (BIN) functionality
            $table->decimal('bin_price', 10, 2)->nullable(); // Buy It Now price
            $table->boolean('accepts_offers')->default(true); // Whether domain accepts offers
            $table->decimal('minimum_offer', 10, 2)->nullable(); // Minimum offer amount
            
            // Commission and marketplace settings
            $table->decimal('commission_rate', 5, 2)->default(5.00); // Default 5% commission
            $table->boolean('featured_listing')->default(false); // Featured listing flag
            $table->timestamp('featured_until')->nullable(); // Featured listing expiry
            
            // Domain verification and trust
            $table->boolean('domain_verified')->default(false); // Domain ownership verified
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_method')->nullable(); // How domain was verified
            
            // SEO and discovery
            $table->json('tags')->nullable(); // Domain tags for better search
            $table->string('meta_title')->nullable(); // SEO meta title
            $table->text('meta_description')->nullable(); // SEO meta description
            
            // Analytics and performance
            $table->integer('view_count')->default(0); // Number of views
            $table->integer('favorite_count')->default(0); // Number of favorites
            $table->integer('offer_count')->default(0); // Number of offers received
            
            // Expiry and renewal
            $table->boolean('auto_renew')->default(false); // Auto-renewal setting
            $table->decimal('renewal_price', 10, 2)->nullable(); // Annual renewal cost
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn([
                'bin_price', 'accepts_offers', 'minimum_offer',
                'commission_rate', 'featured_listing', 'featured_until',
                'domain_verified', 'verified_at', 'verification_method',
                'tags', 'meta_title', 'meta_description',
                'view_count', 'favorite_count', 'offer_count',
                'auto_renew', 'renewal_price'
            ]);
        });
    }
};
