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
            // Make An Offer functionality (only add missing fields)
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
                'maximum_offer', 'auto_accept_offers', 'auto_accept_threshold'
            ]);
        });
    }
};