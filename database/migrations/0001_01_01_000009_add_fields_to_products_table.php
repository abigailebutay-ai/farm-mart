<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add status field (active, inactive, out_of_stock)
            $table->string('status')->default('active')->after('image');
            
            // Add inventory management fields
            $table->integer('low_stock_threshold')->default(10)->after('quantity');
            $table->string('unit')->default('kg')->after('low_stock_threshold');
            $table->date('harvest_date')->nullable()->after('unit');
            $table->string('crop_code')->nullable()->unique()->after('harvest_date');
            
            // Add average rating
            $table->decimal('average_rating', 3, 2)->default(0)->after('crop_code');
            $table->integer('total_reviews')->default(0)->after('average_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'low_stock_threshold',
                'unit',
                'harvest_date',
                'crop_code',
                'average_rating',
                'total_reviews'
            ]);
        });
    }
};
