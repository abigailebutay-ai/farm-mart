<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'refund_status')) {
                $table->string('refund_status')->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('orders', 'refund_reference')) {
                $table->string('refund_reference')->nullable()->after('refund_status');
            }

            if (! Schema::hasColumn('orders', 'refund_note')) {
                $table->text('refund_note')->nullable()->after('refund_reference');
            }

            if (! Schema::hasColumn('orders', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['refunded_at', 'refund_note', 'refund_reference', 'refund_status'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
