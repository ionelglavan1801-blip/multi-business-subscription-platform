<?php

namespace Tests\Feature\Business;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetCurrentBusinessMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_sets_first_business_when_no_session(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        $this->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($business->id, session('current_business_id'));
    }

    public function test_middleware_keeps_existing_session(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business1 = Business::factory()->create(['plan_id' => $plan->id]);
        $business2 = Business::factory()->create(['plan_id' => $plan->id]);
        $business1->users()->attach($user, ['role' => 'owner']);
        $business2->users()->attach($user, ['role' => 'owner']);

        session(['current_business_id' => $business2->id]);

        $this->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($business2->id, session('current_business_id'));
    }

    public function test_middleware_resets_invalid_business(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        // Set session to non-existent business
        session(['current_business_id' => 99999]);

        $this->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($business->id, session('current_business_id'));
    }

    public function test_middleware_resets_unauthorized_business(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $userBusiness = Business::factory()->create(['plan_id' => $plan->id]);
        $otherBusiness = Business::factory()->create(['plan_id' => $plan->id]);
        $userBusiness->users()->attach($user, ['role' => 'owner']);

        // Set session to business user doesn't belong to
        session(['current_business_id' => $otherBusiness->id]);

        $this->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals($userBusiness->id, session('current_business_id'));
    }

    public function test_user_can_get_current_business(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        session(['current_business_id' => $business->id]);

        $currentBusiness = $user->currentBusiness();

        $this->assertNotNull($currentBusiness);
        $this->assertEquals($business->id, $currentBusiness->id);
    }

    public function test_user_can_check_if_owner_of_current_business(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        session(['current_business_id' => $business->id]);

        $this->assertTrue($user->isOwnerOfCurrentBusiness());
    }

    public function test_user_can_check_if_admin_of_current_business(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'admin']);

        session(['current_business_id' => $business->id]);

        $this->assertTrue($user->isAdminOfCurrentBusiness());
        $this->assertFalse($user->isOwnerOfCurrentBusiness());
    }

    public function test_member_is_not_admin_or_owner(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'member']);

        session(['current_business_id' => $business->id]);

        $this->assertFalse($user->isAdminOfCurrentBusiness());
        $this->assertFalse($user->isOwnerOfCurrentBusiness());
    }
}
