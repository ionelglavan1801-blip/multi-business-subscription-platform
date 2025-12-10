<?php

namespace App\Actions\Business;

use App\Models\Business;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\UserInvitedToBusiness;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InviteUserToBusiness
{
    public function execute(Business $business, string $email, string $role = 'member', ?User $invitedBy = null): Invitation
    {
        // Validate role
        if (! in_array($role, ['member', 'admin'])) {
            throw ValidationException::withMessages([
                'role' => 'Invalid role. Must be either member or admin.',
            ]);
        }

        // Check if user is already a member
        $existingUser = User::where('email', $email)->first();
        if ($existingUser && $business->users()->where('user_id', $existingUser->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This user is already a member of the business.',
            ]);
        }

        // Check for pending invitation
        $pendingInvitation = $business->invitations()
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($pendingInvitation) {
            throw ValidationException::withMessages([
                'email' => 'An invitation has already been sent to this email address.',
            ]);
        }

        // Create invitation
        $invitation = Invitation::create([
            'business_id' => $business->id,
            'invited_by' => $invitedBy?->id ?? auth()->id(),
            'email' => $email,
            'role' => $role,
            'token' => Str::uuid(),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        Notification::route('mail', $email)->notify(new UserInvitedToBusiness($invitation));

        return $invitation;
    }
}
