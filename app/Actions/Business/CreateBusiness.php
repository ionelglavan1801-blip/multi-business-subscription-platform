<?php

namespace App\Actions\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Str;

class CreateBusiness
{
    /**
     * Create a new business for the user.
     */
    public function execute(User $user, array $data): Business
    {
        // Generate unique slug
        $slug = $this->generateUniqueSlug($data['name']);

        // Create business
        $business = Business::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'plan_id' => $data['plan_id'],
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
