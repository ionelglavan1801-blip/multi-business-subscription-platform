<?php

namespace Tests\Feature\Project;

use App\Models\Business;
use App\Models\Plan;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPlanLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_business_can_add_more_projects_when_under_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 5]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        Project::factory()->count(3)->create(['business_id' => $business->id]);

        $this->assertTrue($business->canAddMoreProjects());
    }

    public function test_business_cannot_add_more_projects_when_at_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 3]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        Project::factory()->count(3)->create(['business_id' => $business->id]);

        $this->assertFalse($business->canAddMoreProjects());
    }

    public function test_business_can_always_add_projects_when_no_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => null]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        Project::factory()->count(100)->create(['business_id' => $business->id]);

        $this->assertTrue($business->canAddMoreProjects());
    }

    public function test_projects_usage_percentage_is_calculated_correctly(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 10]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        Project::factory()->count(3)->create(['business_id' => $business->id]);

        $this->assertEquals(30, $business->projectsUsagePercentage());
    }

    public function test_projects_usage_percentage_caps_at_100(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 5]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        Project::factory()->count(10)->create(['business_id' => $business->id]);

        $this->assertEquals(100, $business->projectsUsagePercentage());
    }

    public function test_remaining_project_slots_calculated_correctly(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 10]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        Project::factory()->count(3)->create(['business_id' => $business->id]);

        $this->assertEquals(7, $business->remainingProjectSlots());
    }

    public function test_remaining_project_slots_is_null_when_no_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => null]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        $this->assertNull($business->remainingProjectSlots());
    }

    public function test_user_cannot_create_project_when_at_plan_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 2]);
        $user = User::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        Project::factory()->count(2)->create(['business_id' => $business->id]);

        $this->assertFalse($user->can('create', [Project::class, $business]));
    }

    public function test_user_can_create_project_when_under_plan_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 5]);
        $user = User::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        Project::factory()->count(2)->create(['business_id' => $business->id]);

        $this->assertTrue($user->can('create', [Project::class, $business]));
    }

    public function test_create_project_form_returns_403_when_at_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 1]);
        $user = User::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        Project::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get(route('businesses.projects.create', $business));

        $response->assertStatus(403);
    }

    public function test_store_project_returns_403_when_at_limit(): void
    {
        $plan = Plan::factory()->create(['max_projects' => 1]);
        $user = User::factory()->create();
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        Project::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post(route('businesses.projects.store', $business), [
            'name' => 'New Project',
        ]);

        $response->assertStatus(403);
    }

    public function test_business_can_add_more_users_when_under_limit(): void
    {
        $plan = Plan::factory()->create(['max_users_per_business' => 5]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            $business->users()->attach($user, ['role' => 'member']);
        }

        $this->assertTrue($business->canAddMoreUsers());
    }

    public function test_business_cannot_add_more_users_when_at_limit(): void
    {
        $plan = Plan::factory()->create(['max_users_per_business' => 3]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            $business->users()->attach($user, ['role' => 'member']);
        }

        $this->assertFalse($business->canAddMoreUsers());
    }

    public function test_users_usage_percentage_is_calculated_correctly(): void
    {
        $plan = Plan::factory()->create(['max_users_per_business' => 10]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        $users = User::factory()->count(5)->create();
        foreach ($users as $user) {
            $business->users()->attach($user, ['role' => 'member']);
        }

        $this->assertEquals(50, $business->usersUsagePercentage());
    }

    public function test_remaining_user_slots_calculated_correctly(): void
    {
        $plan = Plan::factory()->create(['max_users_per_business' => 10]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);

        $users = User::factory()->count(4)->create();
        foreach ($users as $user) {
            $business->users()->attach($user, ['role' => 'member']);
        }

        $this->assertEquals(6, $business->remainingUserSlots());
    }
}
