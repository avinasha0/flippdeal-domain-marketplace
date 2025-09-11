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
        Schema::create('domain_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->onDelete('cascade');
            $table->enum('method', ['dns_txt', 'dns_cname', 'file_upload', 'whois'])->default('dns_txt');
            $table->string('token')->nullable(); // For DNS TXT verification
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['pending', 'verified', 'failed', 'needs_admin'])->default('pending');
            $table->json('evidence')->nullable(); // Verification evidence and flags
            $table->text('raw_whois')->nullable(); // Raw WHOIS response
            $table->integer('attempts')->default(0);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['domain_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('token');
            $table->index('token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_verifications');
    }
};