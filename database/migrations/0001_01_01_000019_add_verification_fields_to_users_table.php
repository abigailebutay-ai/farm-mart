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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('role');
            }

            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->string('verification_status')->nullable()->after('is_verified');
            }

            if (!Schema::hasColumn('users', 'kyc_documents')) {
                $table->json('kyc_documents')->nullable()->after('verification_status');
            }

            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('kyc_documents');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = collect(['is_verified', 'verification_status', 'kyc_documents', 'verified_at'])
            ->filter(fn (string $column) => Schema::hasColumn('users', $column))
            ->all();

        if (empty($columns)) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
