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
        Schema::table('messages', function (Blueprint $table) {
            // Add new columns for real-time messaging
            $table->foreignId('from_user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->nullable()->after('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('domain_id')->nullable()->after('to_user_id')->constrained('domains')->onDelete('cascade');
            $table->text('body')->nullable()->after('domain_id');
            $table->timestamp('read_at')->nullable()->after('body');
            $table->json('metadata')->nullable()->after('read_at');

            // Add indexes for performance
            $table->index(['from_user_id', 'to_user_id']);
            $table->index(['to_user_id', 'read_at']);
            $table->index(['domain_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['from_user_id', 'to_user_id']);
            $table->dropIndex(['to_user_id', 'read_at']);
            $table->dropIndex(['domain_id']);
            $table->dropIndex(['created_at']);

            // Drop foreign key constraints
            $table->dropForeign(['from_user_id']);
            $table->dropForeign(['to_user_id']);
            $table->dropForeign(['domain_id']);

            // Drop columns
            $table->dropColumn([
                'from_user_id',
                'to_user_id', 
                'domain_id',
                'body',
                'read_at',
                'metadata'
            ]);
        });
    }
};