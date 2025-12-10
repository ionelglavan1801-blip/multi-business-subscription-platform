<?php

namespace Tests\Feature\Invitation;

use App\Models\Business;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_invite_users(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $owner = User::factory()->create();
        $business->users()->attach($owner->id, ['role' => 'owner']);

        $this->actingAs($owner);

        $this->assertTrue($owner->can('create', [Invitation::class, $business]));
    }

    public function test_admin_can_invite_users(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $business->users()->attach($admin->id, ['role' => 'admin']);

        $this->actingAs($admin);

        $this->assertTrue($admin->can('create', [Invitation::class, $business]));
    }

    public function test_member_cannot_invite_users(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'member']);

        $this->actingAs($member);

        $this->assertFalse($member->can('create', [Invitation::class, $business]));
    }

    public function test_inviter_can_cancel_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $inviter = User::factory()->create();
        $business->users()->attach($inviter->id, ['role' => 'admin']);

        $invitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $inviter->id,
        ]);

        $this->actingAs($inviter);

        $this->assertTrue($inviter->can('cancel', $invitation));
    }

    public function test_business_admin_can_cancel_any_invitation(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $admin = User::factory()->create();
        $inviter = User::factory()->create();
        $business->users()->attach($admin->id, ['role' => 'admin']);
        $business->users()->attach($inviter->id, ['role' => 'admin']);

        $invitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $inviter->id,
        ]);

        $this->actingAs($admin);

        $this->assertTrue($admin->can('cancel', $invitation));
    }

    public function test_user_can_accept_invitation_with_matching_email(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $inviter = User::factory()->create();
        $invitee = User::factory()->create(['email' => 'invited@example.com']);

        $invitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($invitee);

        $this->assertTrue($invitee->can('accept', $invitation));
    }

    public function test_user_cannot_accept_invitation_with_different_email(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $inviter = User::factory()->create();
        $wrongUser = User::factory()->create(['email' => 'wrong@example.com']);

        $invitation = Invitation::factory()->create([
            'business_id' => $business->id,
            'invited_by' => $inviter->id,
            'email' => 'invited@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($wrongUser);

        $this->assertFalse($wrongUser->can('accept', $invitation));
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
            'status' => 'pending',
        ]);

        $this->actingAs($invitee);

        $this->assertFalse($invitee->can('accept', $invitation));
    }
}
