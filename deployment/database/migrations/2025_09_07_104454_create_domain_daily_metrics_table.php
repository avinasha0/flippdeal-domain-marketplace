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
        Schema::create('domain_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->onDelete('cascade');
            $table->date('metric_date');
            $table->integer('views')->default(0);
            $table->integer('bids')->default(0);
            $table->integer('offers')->default(0);
            $table->integer('favorites')->default(0);
            $table->integer('watchers')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->timestamps();

            // Unique constraint to prevent duplicate entries for same domain/date
            $table->unique(['domain_id', 'metric_date']);
            
            // Indexes for performance
            $table->index('metric_date');
            $table->index(['domain_id', 'metric_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_daily_metrics');
    }
};