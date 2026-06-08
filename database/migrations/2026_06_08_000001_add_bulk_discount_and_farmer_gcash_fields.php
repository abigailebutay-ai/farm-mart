<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'gcash_name')) {
                $table->string('gcash_name')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'gcash_number')) {
                $table->string('gcash_number', 20)->nullable()->after('gcash_name');
            }

            if (! Schema::hasColumn('users', 'gcash_qr')) {
                $table->string('gcash_qr')->nullable()->after('gcash_number');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'discount_label')) {
                $table->string('discount_label')->nullable()->after('coupon_code');
            }

            if (! Schema::hasColumn('orders', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('discount_label');
            }

            if (! Schema::hasColumn('orders', 'discount_rate')) {
                $table->decimal('discount_rate', 8, 2)->nullable()->after('discount_type');
            }

            if (! Schema::hasColumn('orders', 'gcash_payee_name')) {
                $table->string('gcash_payee_name')->nullable()->after('payment_proof');
            }

            if (! Schema::hasColumn('orders', 'gcash_payee_number')) {
                $table->string('gcash_payee_number', 20)->nullable()->after('gcash_payee_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['discount_label', 'discount_type', 'discount_rate', 'gcash_payee_name', 'gcash_payee_number'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['gcash_name', 'gcash_number', 'gcash_qr'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
