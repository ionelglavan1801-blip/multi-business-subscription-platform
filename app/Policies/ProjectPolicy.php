<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Business $business): bool
    {
        return $user->businesses()->where('business_id', $business->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->businesses()->where('business_id', $project->business_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Business $business): Response
    {
        // User must be a member of the business
        if (! $user->businesses()->where('business_id', $business->id)->exists()) {
            return Response::deny('You are not a member of this business.');
        }

        // Check plan limits
        if (! $business->canAddMoreProjects()) {
            $maxProjects = $business->plan?->max_projects ?? 0;

            return Response::deny("You have reached the maximum number of projects ({$maxProjects}) for your current plan.");
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // User must be a member of the business
        return $user->businesses()->where('business_id', $project->business_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only owner/admin of the business or the project creator can delete
        $pivot = $user->businesses()
            ->where('business_id', $project->business_id)
            ->first()
            ?->pivot;

        if (! $pivot) {
            return false;
        }

        return in_array($pivot->role, ['owner', 'admin']) || $project->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $this->delete($user, $project);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        // Only owner can permanently delete
        $pivot = $user->businesses()
            ->where('business_id', $project->business_id)
            ->first()
            ?->pivot;

        return $pivot && $pivot->role === 'owner';
    }
}
