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
        Schema::table('transactions', function (Blueprint $table) {
            // Add missing kyc_approved_at column first
            if (!Schema::hasColumn('transactions', 'kyc_approved_at')) {
                $table->timestamp('kyc_approved_at')->nullable()->after('kyc_request_id');
            }
            
            // Add escrow checklist fields
            $table->json('seller_checklist')->nullable()->after('kyc_approved_at');
            $table->json('buyer_checklist')->nullable()->after('seller_checklist');
            $table->json('transfer_evidence')->nullable()->after('buyer_checklist');
            $table->timestamp('transfer_initiated_at')->nullable()->after('transfer_evidence');
            $table->timestamp('transfer_completed_at')->nullable()->after('transfer_initiated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'seller_checklist',
                'buyer_checklist',
                'transfer_evidence',
                'transfer_initiated_at',
                'transfer_completed_at',
            ]);
        });
    }
};