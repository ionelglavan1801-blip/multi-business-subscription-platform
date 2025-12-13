<?php

namespace App\Actions\Project;

use App\Models\Project;

class DeleteProject
{
    /**
     * Delete the specified project (soft delete).
     */
    public function execute(Project $project): bool
    {
        return $project->delete();
    }
}
