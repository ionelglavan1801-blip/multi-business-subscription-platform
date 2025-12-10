<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    /**
     * Determine whether the user can invite users to a business.
     */
    public function create(User $user, Business $business): bool
    {
        // Only owners and admins can invite users
        $role = $business->users()->where('user_id', $user->id)->first()?->pivot->role;

        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can cancel an invitation.
     */
    public function cancel(User $user, Invitation $invitation): bool
    {
        // Only the person who sent the invitation or business owner/admin can cancel
        if ($invitation->invited_by === $user->id) {
            return true;
        }

        $role = $invitation->business->users()->where('user_id', $user->id)->first()?->pivot->role;

        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can resend an invitation.
     */
    public function resend(User $user, Invitation $invitation): bool
    {
        // Same as cancel - only inviter or business owner/admin
        return $this->cancel($user, $invitation);
    }

    /**
     * Determine whether the user can accept an invitation.
     */
    public function accept(User $user, Invitation $invitation): bool
    {
        // User can only accept if their email matches the invitation
        return $user->email === $invitation->email
            && $invitation->status === 'pending'
            && ! $invitation->isExpired();
    }
}
