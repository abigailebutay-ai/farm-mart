<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'accepted', 'preparing', 'out_for_delivery', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE orders SET status = 'preparing' WHERE status = 'out_for_delivery'");
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'accepted', 'preparing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
