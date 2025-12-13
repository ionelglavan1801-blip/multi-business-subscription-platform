<?php

namespace App\Actions\Project;

use App\Models\Project;

class UpdateProject
{
    /**
     * Update the specified project.
     */
    public function execute(Project $project, array $data): Project
    {
        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
        ]);

        return $project->fresh();
    }
}
