<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_only_lists_pending_farmer_and_buyer_verifications(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'verification_status' => 'pending',
        ]);
        $farmer = User::factory()->farmer()->create([
            'name' => 'Pending Farmer',
            'verification_status' => 'pending',
            'is_verified' => false,
        ]);
        $consumer = User::factory()->consumer()->create([
            'name' => 'Pending Consumer',
            'verification_status' => 'pending',
            'is_verified' => false,
        ]);
        $approvedFarmer = User::factory()->farmer()->create([
            'name' => 'Approved Farmer',
            'verification_status' => 'approved',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('Pending Farmer')
            ->assertSee('Pending Consumer')
            ->assertSee('Approve')
            ->assertSee('Reject')
            ->assertDontSee(route('admin.users.approve', $approvedFarmer))
            ->assertDontSee(route('admin.users.reject', $approvedFarmer));
    }

    public function test_admin_can_approve_pending_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = User::factory()->farmer()->unverified()->create([
            'verification_status' => 'pending',
            'is_verified' => false,
            'verified_at' => null,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.approve', $farmer))
            ->assertRedirect()
            ->assertSessionHas('success', 'User approved successfully.');

        $farmer->refresh();

        $this->assertTrue($farmer->is_verified);
        $this->assertSame('approved', $farmer->verification_status);
        $this->assertNotNull($farmer->verified_at);
        $this->assertNotNull($farmer->email_verified_at);
    }

    public function test_admin_can_reject_pending_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $consumer = User::factory()->consumer()->create([
            'verification_status' => 'pending',
            'is_verified' => false,
            'verified_at' => null,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.reject', $consumer))
            ->assertRedirect()
            ->assertSessionHas('success', 'User rejected successfully.');

        $consumer->refresh();

        $this->assertFalse($consumer->is_verified);
        $this->assertSame('rejected', $consumer->verification_status);
        $this->assertNull($consumer->verified_at);
    }

    public function test_non_admin_users_cannot_approve_or_reject_users(): void
    {
        $farmer = User::factory()->farmer()->create();
        $consumer = User::factory()->consumer()->create([
            'verification_status' => 'pending',
            'is_verified' => false,
        ]);

        $this->actingAs($farmer)
            ->patch(route('admin.users.approve', $consumer))
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Please login with an authorized account.');

        $this->assertGuest();

        $this->actingAs($consumer)
            ->patch(route('admin.users.reject', $farmer))
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Please login with an authorized account.');
    }
}
