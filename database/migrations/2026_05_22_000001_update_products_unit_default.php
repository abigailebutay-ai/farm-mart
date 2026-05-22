<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'unit')) {
            DB::table('products')
                ->whereNull('unit')
                ->orWhere('unit', '')
                ->orWhere('unit', 'kg')
                ->update(['unit' => 'piece']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep product units as user data once they have been normalized.
    }
};
