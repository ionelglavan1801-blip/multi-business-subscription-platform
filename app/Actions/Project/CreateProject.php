<?php

namespace App\Actions\Project;

use App\Models\Business;
use App\Models\Project;
use App\Models\User;

class CreateProject
{
    /**
     * Create a new project for the business.
     */
    public function execute(Business $business, User $user, array $data): Project
    {
        return Project::create([
            'business_id' => $business->id,
            'created_by' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);
    }
}
