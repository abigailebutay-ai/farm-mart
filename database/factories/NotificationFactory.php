<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => 'system.test',
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(),
            'data' => [
                'icon' => 'bell',
                'url' => route('dashboard'),
            ],
            'read_at' => null,
        ];
    }
}
