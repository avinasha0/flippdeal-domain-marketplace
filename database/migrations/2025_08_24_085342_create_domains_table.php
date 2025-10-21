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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('domain_name');
            $table->string('domain_extension');
            $table->decimal('asking_price', 10, 2);
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('has_website')->default(false);
            $table->boolean('has_traffic')->default(false);
            $table->boolean('premium_domain')->default(false);
            $table->text('additional_features')->nullable();
            $table->enum('status', ['draft', 'active', 'sold', 'inactive'])->default('draft');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['category', 'status']);
            $table->index(['asking_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
