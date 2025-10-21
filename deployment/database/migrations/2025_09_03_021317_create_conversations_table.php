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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('buyer_unread_count')->default(0);
            $table->boolean('seller_unread_count')->default(0);
            $table->timestamps();

            $table->unique(['domain_id', 'buyer_id', 'seller_id']);
            $table->index(['buyer_id', 'last_message_at']);
            $table->index(['seller_id', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};