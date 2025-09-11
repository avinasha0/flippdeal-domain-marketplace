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
            $table->unsignedBigInteger('actor_id')->nullable(); // User who performed the action
            $table->string('action'); // e.g., 'domain.verification.created', 'domain.status.changed'
            $table->string('target_type'); // e.g., 'App\Models\Domain', 'App\Models\Transaction'
            $table->unsignedBigInteger('target_id')->nullable(); // ID of the target model
            $table->json('payload')->nullable(); // Action-specific data (sensitive data masked)
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['actor_id', 'target_type', 'target_id']);
            $table->index(['action', 'created_at']);
            $table->index(['target_type', 'target_id']);
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