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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // Model class name
            $table->unsignedBigInteger('auditable_id'); // Model ID
            $table->string('event'); // created, updated, deleted, state_changed, etc.
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who made the change
            $table->string('user_type')->nullable(); // user, admin, system
            $table->text('description')->nullable(); // Human readable description
            $table->json('metadata')->nullable(); // Additional context data
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('event');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};