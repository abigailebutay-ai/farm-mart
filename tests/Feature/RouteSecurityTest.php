<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_users_are_redirected_from_protected_routes(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/cart')->assertRedirect('/login');
        $this->get('/admin/products')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/farmer/inventory')->assertRedirect('/login');
        $this->get('/farmer/products')->assertRedirect('/login');
        $this->get('/consumer/cart')->assertRedirect('/login');
        $this->get('/buyer/cart')->assertRedirect('/login');
        $this->get('/products/create')->assertRedirect('/login');
        $this->get('/profile/edit')->assertRedirect('/login');
        $this->get('/settings')->assertRedirect('/login');
    }

    public function test_admin_cannot_access_farmer_or_consumer_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/products')->assertOk();

        $this->actingAs($admin)
            ->get('/farmer/inventory')
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Please login with an authorized account.');
        $this->assertGuest();

        $this->actingAs($admin)->get('/consumer/cart')->assertRedirect('/login');
        $this->assertGuest();

        $this->actingAs($admin)->get('/cart')->assertRedirect('/login');
        $this->assertGuest();

        $this->actingAs($admin)->get('/profile/edit')->assertOk();
        $this->actingAs($admin)->get('/settings')->assertOk();
    }

    public function test_farmer_cannot_access_admin_or_consumer_routes(): void
    {
        $farmer = User::factory()->farmer()->create();

        $this->actingAs($farmer)->get('/dashboard')->assertOk();
        $this->actingAs($farmer)->get('/farmer/inventory')->assertOk();
        $this->actingAs($farmer)->get('/profile/edit')->assertOk();
        $this->actingAs($farmer)->get('/settings')->assertOk();

        $this->actingAs($farmer)
            ->get('/admin/dashboard')
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Please login with an authorized account.');
        $this->assertGuest();

        $this->actingAs($farmer)->get('/consumer/marketplace')->assertRedirect('/login');
        $this->assertGuest();

        $this->actingAs($farmer)->get('/cart')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_consumer_cannot_access_admin_or_farmer_routes(): void
    {
        $consumer = User::factory()->consumer()->create();
        $product = Product::factory()->create();

        $this->actingAs($consumer)->get('/dashboard')->assertOk();
        $this->actingAs($consumer)->get('/consumer/marketplace')->assertOk();
        $this->actingAs($consumer)->get('/cart')->assertOk();
        $this->actingAs($consumer)->get('/profile/edit')->assertOk();
        $this->actingAs($consumer)->get('/settings')->assertOk();

        $this->actingAs($consumer)
            ->get('/admin/products')
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Please login with an authorized account.');
        $this->assertGuest();

        $this->actingAs($consumer)->get('/farmer/inventory')->assertRedirect('/login');
        $this->assertGuest();

        $this->actingAs($consumer)->get('/products/create')->assertRedirect('/login');
        $this->assertGuest();

        $this->actingAs($consumer)->get("/products/{$product->id}/edit")->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_consumers_can_only_open_their_own_completed_receipts(): void
    {
        $consumer = User::factory()->consumer()->create();
        $otherConsumer = User::factory()->consumer()->create();
        $ownOrder = Order::factory()->for($consumer, 'consumer')->create(['status' => 'completed']);
        $otherOrder = Order::factory()->for($otherConsumer, 'consumer')->create(['status' => 'completed']);
        $pendingOrder = Order::factory()->for($consumer, 'consumer')->create(['status' => 'pending']);

        $this->actingAs($consumer)->get(route('consumer.orders.receipt', $ownOrder))->assertOk();
        $this->actingAs($consumer)->get(route('consumer.orders.receipt', $otherOrder))->assertForbidden();
        $this->actingAs($consumer)->get(route('consumer.orders.receipt', $pendingOrder))->assertNotFound();
    }
}
