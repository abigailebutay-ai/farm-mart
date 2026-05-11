<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Vegetables', 'Fruits', 'Dairy', 'Grains', 'Herbs', 'Meat', 'Fish'];

        return [
            'user_id' => User::factory()->farmer(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement($categories),
            'price' => fake()->randomFloat(2, 50, 500),
            'quantity' => fake()->numberBetween(10, 1000),
            'image' => null,
        ];
    }
}
