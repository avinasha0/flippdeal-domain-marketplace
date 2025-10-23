<?php
/**
 * Migration to add pending_user_data to email_activation_tokens table
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_activation_tokens', function (Blueprint $table) {
            $table->json('pending_user_data')->nullable()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('email_activation_tokens', function (Blueprint $table) {
            $table->dropColumn('pending_user_data');
        });
    }
};
