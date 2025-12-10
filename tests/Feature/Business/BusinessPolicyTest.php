<?php

namespace Tests\Feature\Business;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_any_businesses(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('viewAny', Business::class));
    }

    public function test_user_can_view_business_they_belong_to(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $this->assertTrue($user->can('view', $business));
    }

    public function test_user_cannot_view_business_they_dont_belong_to(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();

        $this->assertFalse($user->can('view', $business));
    }

    public function test_user_can_create_business_when_under_plan_limit(): void
    {
        $plan = Plan::factory()->create(['max_businesses' => 3]);
        $user = User::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        $this->assertTrue($user->can('create', Business::class));
    }

    public function test_user_cannot_create_business_when_at_plan_limit(): void
    {
        $plan = Plan::factory()->create(['max_businesses' => 1]);
        $user = User::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        $this->assertFalse($user->can('create', Business::class));
    }

    public function test_owner_can_update_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $this->assertTrue($user->can('update', $business));
    }

    public function test_admin_can_update_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'admin']);

        $this->assertTrue($user->can('update', $business));
    }

    public function test_member_cannot_update_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $this->assertFalse($user->can('update', $business));
    }

    public function test_owner_can_delete_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $this->assertTrue($user->can('delete', $business));
    }

    public function test_admin_cannot_delete_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'admin']);

        $this->assertFalse($user->can('delete', $business));
    }

    public function test_member_cannot_delete_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $this->assertFalse($user->can('delete', $business));
    }

    public function test_owner_can_manage_billing(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $this->assertTrue($user->can('manageBilling', $business));
    }

    public function test_admin_cannot_manage_billing(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'admin']);

        $this->assertFalse($user->can('manageBilling', $business));
    }

    public function test_owner_can_invite_users(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $this->assertTrue($user->can('inviteUsers', $business));
    }

    public function test_admin_can_invite_users(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'admin']);

        $this->assertTrue($user->can('inviteUsers', $business));
    }

    public function test_member_cannot_invite_users(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $this->assertFalse($user->can('inviteUsers', $business));
    }
}
