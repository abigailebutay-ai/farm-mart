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
            $table->enum('role', ['farmer', 'consumer'])->default('consumer')->after('remember_token');
            $table->string('phone')->nullable()->after('role');
            $table->text('address')->nullable()->after('phone');
            $table->string('profile_picture')->nullable()->after('address');
            $table->boolean('dark_mode')->default(false)->after('profile_picture');
            $table->boolean('notification_enabled')->default(true)->after('dark_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'profile_picture', 'dark_mode', 'notification_enabled']);
        });
    }
};
