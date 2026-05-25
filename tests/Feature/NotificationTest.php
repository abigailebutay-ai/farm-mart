<?php

namespace Tests\Feature;

use App\Models\Notification;
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
}
