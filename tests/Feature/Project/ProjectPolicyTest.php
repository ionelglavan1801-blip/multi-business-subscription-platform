<?php

namespace Tests\Feature\Project;

use App\Models\Business;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_any_projects_for_their_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $this->assertTrue($user->can('viewAny', [Project::class, $business]));
    }

    public function test_user_cannot_view_any_projects_for_other_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();

        $this->assertFalse($user->can('viewAny', [Project::class, $business]));
    }

    public function test_user_can_view_project_they_belong_to(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue($user->can('view', $project));
    }

    public function test_user_cannot_view_project_from_other_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();

        $project = Project::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->assertFalse($user->can('view', $project));
    }

    public function test_member_can_create_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $this->assertTrue($user->can('create', [Project::class, $business]));
    }

    public function test_user_cannot_create_project_for_other_business(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();

        $this->assertFalse($user->can('create', [Project::class, $business]));
    }

    public function test_member_can_update_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->assertTrue($user->can('update', $project));
    }

    public function test_owner_can_delete_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->assertTrue($user->can('delete', $project));
    }

    public function test_admin_can_delete_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'admin']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->assertTrue($user->can('delete', $project));
    }

    public function test_creator_can_delete_their_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue($user->can('delete', $project));
    }

    public function test_member_cannot_delete_other_users_project(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'member']);
        $business->users()->attach($otherUser, ['role' => 'member']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $otherUser->id,
        ]);

        $this->assertFalse($user->can('delete', $project));
    }

    public function test_only_owner_can_force_delete_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->assertTrue($user->can('forceDelete', $project));
    }

    public function test_admin_cannot_force_delete_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'admin']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
        ]);

        $this->assertFalse($user->can('forceDelete', $project));
    }
}
