<?php

namespace Tests\Feature\Invitation;

use App\Models\Business;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\UserInvitedToBusiness;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InvitationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_admin_can_send_invitation(): void
    {
        Notification::fake();

        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $business->users()->attach($admin->id, ['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('businesses.invitations.store', $business), [
            'email' => 'newuser@example.com',
            'role' => 'member',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invitations', [
            'business_id' => $business->id,
            'email' => 'newuser@example.com',
            'role' => 'member',
            'status' => 'pending',
        ]);

        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            UserInvitedToBusiness::class
        );
    }

    public function test_member_cannot_send_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->post(route('businesses.invitations.store', $business), [
            'email' => 'newuser@example.com',
            'role' => 'member',
        ]);

        $response->assertForbidden();
    }

    public function test_cannot_invite_existing_member(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $existingMember = User::factory()->create(['email' => 'existing@example.com']);
        $business->users()->attach($admin->id, ['role' => 'admin']);
        $business->users()->attach($existingMember->id, ['role' => 'member']);

        $response = $this->actingAs($admin)->post(route('businesses.invitations.store', $business), [
            'email' => 'existing@example.com',
            'role' => 'member',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_cannot_send_duplicate_pending_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $business->users()->attach($admin->id, ['role' => 'admin']);

        Invitation::factory()->create([
            'business_id' => $business->id,
            'email' => 'invited@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('businesses.invitations.store', $business), [
            'email' => 'invited@example.com',
            'role' => 'member',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_accept_valid_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $inviter = User::factory()->create();
        $invitee = User::factory()->create(['email' => 'invited@example.com']);

        $invitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'role' => 'member',
            'status' => 'pending',
            'expires_at' => now()->addDays(7), // Explicitly set future expiry
        ]);

        $response = $this->actingAs($invitee)->post(route('invitations.accept', $invitation->token));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();

        // Verify user was added to business
        $this->assertDatabaseHas('business_user', [
            'business_id' => $business->id,
            'user_id' => $invitee->id,
            'role' => 'member',
        ]);

        // Verify invitation was marked as accepted
        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'status' => 'accepted',
        ]);
    }

    public function test_user_cannot_accept_expired_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $inviter = User::factory()->create();
        $invitee = User::factory()->create(['email' => 'invited@example.com']);

        $invitation = Invitation::factory()->expired()->create([
            'business_id' => $business->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'role' => 'member',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($invitee)->post(route('invitations.accept', $invitation->token));

        // Policy denies access (302 redirect or 403)
        $this->assertTrue(in_array($response->status(), [302, 403]));

        // Verify invitation was NOT accepted
        $this->assertDatabaseMissing('business_user', [
            'business_id' => $business->id,
            'user_id' => $invitee->id,
        ]);

        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'status' => 'pending', // Still pending, not accepted
        ]);
    }

    public function test_admin_can_cancel_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $business->users()->attach($admin->id, ['role' => 'admin']);

        $invitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $admin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->delete(route('businesses.invitations.destroy', [$business, $invitation]));

        $response->assertRedirect();

        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_admin_can_resend_invitation(): void
    {
        Notification::fake();

        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $business->users()->attach($admin->id, ['role' => 'admin']);

        $oldInvitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $admin->id,
            'email' => 'resend@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('businesses.invitations.resend', [$business, $oldInvitation]));

        $response->assertRedirect();

        $this->assertDatabaseHas('invitations', [
            'id' => $oldInvitation->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('invitations', [
            'business_id' => $business->id,
            'email' => 'resend@example.com',
            'status' => 'pending',
        ]);

        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            UserInvitedToBusiness::class
        );
    }
}
