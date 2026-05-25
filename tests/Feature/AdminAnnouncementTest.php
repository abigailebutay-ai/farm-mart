<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_announcements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Market day update',
                'body' => 'Fresh product deliveries are available this weekend.',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.announcements.index'))
            ->assertSessionHas('success', 'Announcement created successfully.');

        $announcement = Announcement::firstOrFail();

        $this->assertSame('Market day update', $announcement->title);
        $this->assertSame($admin->id, $announcement->created_by);
        $this->assertNotNull($announcement->published_at);

        $this->actingAs($admin)
            ->put(route('admin.announcements.update', $announcement), [
                'title' => 'Updated market day',
                'body' => 'Updated delivery details.',
                'status' => 'draft',
            ])
            ->assertRedirect(route('admin.announcements.index'))
            ->assertSessionHas('success', 'Announcement updated successfully.');

        $announcement->refresh();
        $this->assertSame('Updated market day', $announcement->title);
        $this->assertSame('draft', $announcement->status);
        $this->assertNull($announcement->published_at);

        $this->actingAs($admin)
            ->delete(route('admin.announcements.destroy', $announcement))
            ->assertRedirect(route('admin.announcements.index'))
            ->assertSessionHas('success', 'Announcement deleted successfully.');

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_non_admin_users_cannot_manage_announcements(): void
    {
        $farmer = User::factory()->farmer()->create();

        $this->actingAs($farmer)
            ->get(route('admin.announcements.create'))
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Please login with an authorized account.');

        $this->assertGuest();
    }
}
