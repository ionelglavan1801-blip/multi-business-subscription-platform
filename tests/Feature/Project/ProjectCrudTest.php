<?php

namespace Tests\Feature\Project;

use App\Models\Business;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_user_can_view_projects_index(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        Project::factory()->count(3)->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('businesses.projects.index', $business));

        $response->assertStatus(200);
        $response->assertViewIs('projects.index');
        $response->assertViewHas('projects');
    }

    public function test_user_can_view_create_project_form(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->get(route('businesses.projects.create', $business));

        $response->assertStatus(200);
        $response->assertViewIs('projects.create');
    }

    public function test_user_can_create_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->post(route('businesses.projects.store', $business), [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'business_id' => $business->id,
            'created_by' => $user->id,
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'active',
        ]);
    }

    public function test_user_can_view_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('businesses.projects.show', [$business, $project]));

        $response->assertStatus(200);
        $response->assertViewIs('projects.show');
        $response->assertViewHas('project');
    }

    public function test_user_can_view_edit_project_form(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('businesses.projects.edit', [$business, $project]));

        $response->assertStatus(200);
        $response->assertViewIs('projects.edit');
    }

    public function test_user_can_update_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('businesses.projects.update', [$business, $project]), [
            'name' => 'Updated Project',
            'description' => 'Updated Description',
            'status' => 'archived',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'description' => 'Updated Description',
            'status' => 'archived',
        ]);
    }

    public function test_user_can_delete_project(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $project = Project::factory()->create([
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('businesses.projects.destroy', [$business, $project]));

        $response->assertRedirect(route('businesses.projects.index', $business));
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_project_name_is_required(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->post(route('businesses.projects.store', $business), [
            'name' => '',
            'description' => 'Test Description',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
