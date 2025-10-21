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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable(); // User's personal notes about this domain
            $table->boolean('notify_on_price_change')->default(true); // Email notifications
            $table->boolean('notify_on_status_change')->default(true); // Status change notifications
            $table->timestamps();
            
            // Ensure user can only favorite a domain once
            $table->unique(['user_id', 'domain_id']);
            
            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['domain_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
