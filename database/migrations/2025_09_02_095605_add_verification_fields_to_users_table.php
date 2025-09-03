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
        Schema::table('users', function (Blueprint $table) {
            // PayPal verification
            $table->string('paypal_email')->nullable();
            $table->boolean('paypal_verified')->default(false);
            $table->timestamp('paypal_verified_at')->nullable();
            
            // Government ID verification
            $table->string('government_id_path')->nullable(); // Path to uploaded ID file
            $table->boolean('government_id_verified')->default(false);
            $table->timestamp('government_id_verified_at')->nullable();
            $table->text('government_id_rejection_reason')->nullable();
            
            // Two-factor authentication
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();
            
            // Account status
            $table->enum('account_status', ['active', 'suspended', 'pending_verification'])->default('pending_verification');
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            
            // Additional profile fields
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->json('social_links')->nullable(); // LinkedIn, Twitter, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_email', 'paypal_verified', 'paypal_verified_at',
                'government_id_path', 'government_id_verified', 'government_id_verified_at', 'government_id_rejection_reason',
                'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes',
                'account_status', 'suspended_at', 'suspension_reason',
                'company_name', 'website', 'location', 'social_links'
            ]);
        });
    }
};