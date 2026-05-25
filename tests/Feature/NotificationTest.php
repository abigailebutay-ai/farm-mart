<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_notifies_admins_about_pending_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post(route('register'), [
            'name' => 'New Farmer',
            'email' => 'farmer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'farmer',
            'phone' => '09123456789',
            'address' => 'Farm Street',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'type' => 'user.registered',
            'title' => 'New account awaiting verification',
        ]);
    }

    public function test_users_can_mark_only_their_own_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->for($user)->create(['read_at' => null]);
        $otherNotification = Notification::factory()->for($otherUser)->create(['read_at' => null]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($user)
            ->post(route('notifications.read', $otherNotification))
            ->assertForbidden();

        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_users_can_mark_all_their_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Notification::factory()->count(2)->for($user)->create(['read_at' => null]);
        $otherNotification = Notification::factory()->for($otherUser)->create(['read_at' => null]);

        $this->actingAs($user)
            ->post(route('notifications.read-all'))
            ->assertRedirect()
            ->assertSessionHas('success', 'All notifications marked as read.');

        $this->assertSame(0, $user->unreadNotifications()->count());
        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_farmer_can_restock_owned_product_from_inventory(): void
    {
        $farmer = User::factory()->farmer()->create();
        $product = Product::factory()->for($farmer, 'farmer')->create([
            'quantity' => 5,
            'unit' => 'kg',
        ]);

        $this->actingAs($farmer)
            ->patch(route('farmer.products.restock', $product), [
                'quantity' => 20,
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Product restocked successfully.');

        $this->assertSame(25, $product->fresh()->quantity);
    }

    public function test_farmer_cannot_restock_another_farmers_product(): void
    {
        $farmer = User::factory()->farmer()->create();
        $product = Product::factory()->create(['quantity' => 5]);

        $this->actingAs($farmer)
            ->patch(route('farmer.products.restock', $product), [
                'quantity' => 20,
            ])
            ->assertForbidden();

        $this->assertSame(5, $product->fresh()->quantity);
    }

    public function test_order_status_update_notifies_buyer_and_low_stock_crossing_notifies_farmer(): void
    {
        $farmer = User::factory()->farmer()->create();
        $buyer = User::factory()->consumer()->create();
        $product = Product::factory()->for($farmer, 'farmer')->create([
            'quantity' => 12,
            'unit' => 'kg',
        ]);
        $order = Order::factory()->for($buyer, 'consumer')->create(['status' => 'accepted']);
        OrderItem::factory()->for($order)->create([
            'product_id' => $product->id,
            'farmer_id' => $farmer->id,
            'quantity' => 3,
            'price' => $product->price,
            'subtotal' => $product->price * 3,
        ]);

        $this->actingAs($farmer)
            ->put(route('orders.update-status', $order), [
                'status' => 'completed',
            ])
            ->assertRedirect(route('orders.show', $order))
            ->assertSessionHas('success', 'Order status updated!');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $buyer->id,
            'type' => 'order.status_updated',
            'title' => 'Order status updated',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $farmer->id,
            'type' => 'product.low_stock',
            'title' => 'Low stock alert',
        ]);

        $this->assertSame(9, $product->fresh()->quantity);
    }
}
