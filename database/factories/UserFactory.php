<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(['farmer', 'consumer']),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => null,
            'dark_mode' => fake()->boolean(),
            'notification_enabled' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a farmer.
     */
    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'farmer',
        ]);
    }

    /**
     * Indicate that the user is a consumer.
     */
    public function consumer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'consumer',
        ]);
    }
}
