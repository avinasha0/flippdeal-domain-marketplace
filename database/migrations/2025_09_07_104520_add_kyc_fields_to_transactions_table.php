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
            $table->boolean('kyc_required')->default(false)->after('escrow_state');
            $table->boolean('kyc_approved')->default(false)->after('kyc_required');
            $table->foreignId('kyc_request_id')->nullable()->constrained('kyc_requests')->onDelete('set null')->after('kyc_approved');
            $table->timestamp('kyc_approved_at')->nullable()->after('kyc_request_id');
            
            // Index for performance
            $table->index('kyc_required');
            $table->index('kyc_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['kyc_request_id']);
            $table->dropIndex(['kyc_required']);
            $table->dropIndex(['kyc_approved']);
            $table->dropColumn(['kyc_required', 'kyc_approved', 'kyc_request_id', 'kyc_approved_at']);
        });
    }
};