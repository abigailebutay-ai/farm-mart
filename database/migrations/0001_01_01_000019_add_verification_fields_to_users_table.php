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
            $table->boolean('is_verified')->default(false)->after('role');
            $table->string('verification_status')->nullable()->after('is_verified');
            $table->json('kyc_documents')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('kyc_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verification_status', 'kyc_documents', 'verified_at']);
        });
    }
};
