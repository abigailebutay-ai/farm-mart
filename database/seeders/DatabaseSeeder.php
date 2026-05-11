<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test farmer account
        $farmer = User::factory()->farmer()->create([
            'name' => 'John Farmer',
            'email' => 'farmer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create test consumer account
        $consumer = User::factory()->consumer()->create([
            'name' => 'Jane Consumer',
            'email' => 'consumer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample farmers with products
        $farmers = User::factory(5)->farmer()->create();
        
        // Create products for each farmer
        foreach ($farmers as $farmr) {
            Product::factory(8)->create([
                'user_id' => $farmr->id,
            ]);
        }

        // Create sample consumers with carts
        $consumers = User::factory(10)->consumer()->create();

        foreach ($consumers as $cons) {
            // Create cart for each consumer
            $cart = Cart::create([
                'user_id' => $cons->id,
                'subtotal' => 0,
                'total' => 0,
            ]);

            // Add random products to cart
            $productsForCart = Product::inRandomOrder()->limit(rand(2, 5))->get();
            
            foreach ($productsForCart as $product) {
                $quantity = rand(1, 3);
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $quantity * $product->price,
                ]);
            }

            // Calculate cart totals
            $cart->calculateTotals();
        }

        // Create sample orders
        for ($i = 0; $i < 20; $i++) {
            $consumer = $consumers->random();
            $productsForOrder = Product::inRandomOrder()->limit(rand(2, 4))->get();
            $total = 0;

            $order = Order::create([
                'user_id' => $consumer->id,
                'subtotal' => 0,
                'total' => 0,
                'status' => collect(['pending', 'accepted', 'completed', 'cancelled'])->random(),
            ]);

            foreach ($productsForOrder as $product) {
                $quantity = rand(1, 5);
                $subtotal = $quantity * $product->price;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'farmer_id' => $product->user_id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update([
                'subtotal' => $total,
                'total' => $total,
            ]);
        }
    }
}

