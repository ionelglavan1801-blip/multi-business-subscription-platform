<?php

namespace App\Actions\Business;

use App\Models\Business;
use Illuminate\Support\Str;

class UpdateBusiness
{
    /**
     * Update an existing business.
     */
    public function execute(Business $business, array $data): Business
    {
        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ];

        // Update slug if name changed
        if ($business->name !== $data['name']) {
            $updateData['slug'] = $this->generateUniqueSlug($data['name'], $business->id);
        }

        $business->update($updateData);

        return $business->fresh();
    }

    /**
     * Generate a unique slug for the business.
     */
    private function generateUniqueSlug(string $name, int $excludeId): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Business::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
