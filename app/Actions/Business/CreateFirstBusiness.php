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
        // Get Free plan
        $freePlan = Plan::where('slug', 'free')->firstOrFail();

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
