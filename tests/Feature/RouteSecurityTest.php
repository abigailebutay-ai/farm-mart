<?php

namespace Tests\Feature;

use App\Models\Order;
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
        $this->get('/farmer/inventory')->assertRedirect('/login');
    }

    public function test_admin_cannot_access_farmer_or_consumer_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/products')->assertOk();
        $this->actingAs($admin)->get('/farmer/inventory')->assertForbidden();
        $this->actingAs($admin)->get('/consumer/marketplace')->assertForbidden();
        $this->actingAs($admin)->get('/cart')->assertForbidden();
        $this->actingAs($admin)->get('/settings')->assertForbidden();
    }

    public function test_farmer_cannot_access_admin_or_consumer_routes(): void
    {
        $farmer = User::factory()->farmer()->create();

        $this->actingAs($farmer)->get('/dashboard')->assertOk();
        $this->actingAs($farmer)->get('/farmer/inventory')->assertOk();
        $this->actingAs($farmer)->get('/settings')->assertOk();
        $this->actingAs($farmer)->get('/admin/products')->assertForbidden();
        $this->actingAs($farmer)->get('/consumer/marketplace')->assertForbidden();
        $this->actingAs($farmer)->get('/cart')->assertForbidden();
    }

    public function test_consumer_cannot_access_admin_farmer_or_farmer_settings_routes(): void
    {
        $consumer = User::factory()->consumer()->create();

        $this->actingAs($consumer)->get('/dashboard')->assertOk();
        $this->actingAs($consumer)->get('/consumer/marketplace')->assertOk();
        $this->actingAs($consumer)->get('/cart')->assertOk();
        $this->actingAs($consumer)->get('/admin/products')->assertForbidden();
        $this->actingAs($consumer)->get('/farmer/inventory')->assertForbidden();
        $this->actingAs($consumer)->get('/settings')->assertForbidden();
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
