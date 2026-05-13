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
        Schema::create('demand_trends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('period'); // e.g., 'daily', 'weekly', 'monthly'
            $table->decimal('demand_score', 5, 2)->default(0); // 0-100 scale
            $table->string('trend_direction')->default('stable'); // 'increasing', 'decreasing', 'stable'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_trends');
    }
};
