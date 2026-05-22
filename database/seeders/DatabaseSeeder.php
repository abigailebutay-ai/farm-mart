<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $admin->forceFill([
            'role' => 'admin',
            'is_verified' => true,
            'verification_status' => 'approved',
            'email_verified_at' => $admin->email_verified_at ?? now(),
            'verified_at' => $admin->verified_at ?? now(),
        ])->save();
    }
}
