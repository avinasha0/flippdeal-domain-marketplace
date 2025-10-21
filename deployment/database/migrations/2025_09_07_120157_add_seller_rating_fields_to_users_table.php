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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('seller_rating_avg', 3, 2)->nullable()->after('government_id_verified_at');
            $table->integer('seller_rating_count')->default(0)->after('seller_rating_avg');
            $table->integer('total_sales_count')->default(0)->after('seller_rating_count');
            $table->integer('avg_response_time_hours')->nullable()->after('total_sales_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'seller_rating_avg',
                'seller_rating_count',
                'total_sales_count',
                'avg_response_time_hours',
            ]);
        });
    }
};