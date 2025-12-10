<?php

namespace App\Actions\Business;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;

class CreateFirstBusiness
{
    /**
     * Create the first business for a newly registered user.
     */
    public function execute(User $user, ?string $businessName = null): Business
    {
        // Get Free plan (or first available plan)
        $freePlan = Plan::where('slug', 'free')->first() ?? Plan::first();

        // If no plan exists, create a default free plan
        if (! $freePlan) {
            $freePlan = Plan::create([
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Free plan',
                'price_monthly' => 0,
                'max_businesses' => 1,
                'max_users_per_business' => 3,
                'max_projects' => 3,
            ]);
        }

        // Generate business name and slug
        $name = $businessName ?? "{$user->name}'s Business";
        $slug = $this->generateUniqueSlug($name);

        // Create business
        $business = Business::create([
            'name' => $name,
            'slug' => $slug,
            'description' => 'My first business',
            'plan_id' => $freePlan->id,
        ]);

        // Attach user as owner
        $business->users()->attach($user->id, [
            'role' => 'owner',
        ]);

        return $business;
    }

    /**
     * Generate a unique slug for the business.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Business::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
