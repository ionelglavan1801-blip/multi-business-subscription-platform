<?php

namespace App\Actions\Business;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AcceptInvitation
{
    public function execute(Invitation $invitation, User $user): void
    {
        // Validate invitation status
        if ($invitation->status !== 'pending') {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation has already been '.$invitation->status.'.',
            ]);
        }

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation has expired.',
            ]);
        }

        // Check if user email matches invitation
        if ($user->email !== $invitation->email) {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation was sent to a different email address.',
            ]);
        }

        // Check if user is already a member
        if ($invitation->business->users()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'invitation' => 'You are already a member of this business.',
            ]);
        }

        // Attach user to business with role
        $invitation->business->users()->attach($user->id, [
            'role' => $invitation->role,
        ]);

        // Mark invitation as accepted
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }
}
