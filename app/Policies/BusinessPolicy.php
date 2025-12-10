<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Users can view their own businesses
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Business $business): bool
    {
        // User must be a member of the business
        return $user->businesses()->where('business_id', $business->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        // Check if user has reached their plan limit
        $userBusinessesCount = $user->businesses()->count();
        $maxBusinesses = $user->businesses()
            ->first()
            ?->plan
            ->max_businesses ?? 1;

        if ($userBusinessesCount >= $maxBusinesses) {
            return Response::deny("You have reached the maximum number of businesses ({$maxBusinesses}) for your current plan.");
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): bool
    {
        // Only owner or admin can update business
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && in_array($pivot->role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Business $business): bool
    {
        // Only owner can delete business
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Business $business): bool
    {
        // Only owner can restore business
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Business $business): bool
    {
        // Only owner can force delete business
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Determine whether the user can manage billing for the business.
     */
    public function manageBilling(User $user, Business $business): bool
    {
        // Only owner can manage billing
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Determine whether the user can invite users to the business.
     */
    public function inviteUsers(User $user, Business $business): bool
    {
        // Owner or admin can invite users
        $pivot = $user->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && in_array($pivot->role, ['owner', 'admin']);
    }
}
