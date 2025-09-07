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
        Schema::create('domain_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->string('transfer_method', 50); // 'registrar', 'dns', 'manual', 'auth_code'
            $table->json('evidence_data')->nullable(); // Transfer evidence (screenshots, codes, etc.)
            $table->string('evidence_url')->nullable(); // URL to uploaded evidence file
            $table->text('transfer_notes')->nullable(); // Additional notes from seller
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable(); // Admin verification notes
            $table->timestamps();

            // Indexes for performance
            $table->index(['domain_id', 'verified']);
            $table->index(['transaction_id']);
            $table->index(['from_user_id', 'to_user_id']);
            $table->index('verified');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_transfers');
    }
};