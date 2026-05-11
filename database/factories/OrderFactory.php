<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 500, 5000);

        return [
            'user_id' => User::factory()->consumer(),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'status' => fake()->randomElement(['pending', 'accepted', 'completed', 'cancelled']),
            'notes' => fake()->paragraph(),
        ];
    }
}
