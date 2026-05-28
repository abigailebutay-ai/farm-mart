<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'fulfillment_method')) {
                $table->string('fulfillment_method')->nullable()->after('payment_proof');
            }

            if (! Schema::hasColumn('orders', 'completion_proof')) {
                $table->string('completion_proof')->nullable()->after('fulfillment_method');
            }

            if (! Schema::hasColumn('orders', 'completion_note')) {
                $table->text('completion_note')->nullable()->after('completion_proof');
            }

            if (! Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('completion_note');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'accepted', 'preparing', 'ready_for_pickup', 'out_for_delivery', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE orders SET status = 'preparing' WHERE status = 'ready_for_pickup'");
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'accepted', 'preparing', 'out_for_delivery', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('orders', function (Blueprint $table) {
            foreach (['completed_at', 'completion_note', 'completion_proof', 'fulfillment_method'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
