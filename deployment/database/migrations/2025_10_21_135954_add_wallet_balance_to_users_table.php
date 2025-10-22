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
            $table->decimal('wallet_balance', 15, 2)->default(0.00)->after('social_links');
            $table->decimal('total_earnings', 15, 2)->default(0.00)->after('wallet_balance');
            $table->decimal('total_withdrawals', 15, 2)->default(0.00)->after('total_earnings');
            $table->timestamp('last_withdrawal_at')->nullable()->after('total_withdrawals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_balance', 'total_earnings', 'total_withdrawals', 'last_withdrawal_at']);
        });
    }
};
