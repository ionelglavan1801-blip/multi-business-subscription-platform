<?php

namespace Tests\Feature\Business;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateFirstBusinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_business_is_created_on_registration(): void
    {
        // Arrange: Seed plans
        $this->seed(\Database\Seeders\PlanSeeder::class);

        // Act: Register a new user (without CSRF middleware for testing)
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        // Assert: User is created and redirected
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);

        // Assert: First business was created
        // Note: User might have multiple businesses if middleware creates one
        $this->assertGreaterThanOrEqual(1, $user->businesses->count());

        // Find the auto-created business (should have name based on user's name)
        $business = $user->businesses()->where('name', "John Doe's Business")->first()
            ?? $user->businesses->first();

        $this->assertNotNull($business);
        $this->assertEquals("John Doe's Business", $business->name);
        $this->assertEquals('john-does-business', $business->slug);
        $this->assertEquals('owner', $business->pivot->role);

        // Assert: Business is on Free plan
        $this->assertEquals('free', $business->plan->slug);
    }

    public function test_business_slug_is_unique(): void
    {
        // Arrange: Seed plans and create existing business
        $this->seed(\Database\Seeders\PlanSeeder::class);
        $freePlan = Plan::where('slug', 'free')->first();

        Business::create([
            'name' => "Test's Business",
            'slug' => 'tests-business',
            'plan_id' => $freePlan->id,
        ]);

        // Act: Register user with same name (without CSRF middleware for testing)
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/register', [
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $user = User::where('email', 'test@example.com')->first();
        $business = $user->businesses->first();

        // Assert: Slug is incremented
        $this->assertEquals('tests-business-1', $business->slug);
    }
}
