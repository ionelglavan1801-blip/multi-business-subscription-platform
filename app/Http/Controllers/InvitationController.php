<?php

namespace App\Http\Controllers;

use App\Actions\Business\AcceptInvitation;
use App\Actions\Business\InviteUserToBusiness;
use App\Models\Business;
use App\Models\Invitation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvitationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Send an invitation to a user.
     */
    public function store(Request $request, Business $business, InviteUserToBusiness $inviteAction): RedirectResponse
    {
        $this->authorize('create', [Invitation::class, $business]);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:member,admin'],
        ]);

        try {
            $inviteAction->execute($business, $validated['email'], $validated['role']);

            return back()->with('success', 'Invitation sent successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Show the accept invitation page.
     */
    public function show(string $token): View|RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        // Check if invitation is valid
        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'This invitation has already been '.$invitation->status.'.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('dashboard')
                ->with('error', 'This invitation has expired.');
        }

        $invitationData = [
            'token' => $invitation->token,
            'business_name' => $invitation->business->name,
            'role' => $invitation->role,
            'inviter_name' => $invitation->inviter->name,
            'expires_at' => $invitation->expires_at->format('F j, Y \a\t g:i A'),
        ];

        return view('invitations.accept', ['invitation' => $invitationData]);
    }

    /**
     * Accept an invitation.
     */
    public function accept(string $token, AcceptInvitation $acceptAction): RedirectResponse
    {
        $invitation = Invitation::with('business')->where('token', $token)->firstOrFail();

        $this->authorize('accept', $invitation);

        try {
            $acceptAction->execute($invitation, Auth::user());

            return redirect()->route('dashboard')
                ->with('success', 'You have successfully joined '.$invitation->business->name.'.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Cancel an invitation.
     */
    public function destroy(Business $business, Invitation $invitation): RedirectResponse
    {
        $this->authorize('cancel', $invitation);

        if ($invitation->business_id !== $business->id) {
            abort(404);
        }

        $invitation->update(['status' => 'cancelled']);

        return back()->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Resend an invitation.
     */
    public function resend(Business $business, Invitation $invitation, InviteUserToBusiness $inviteAction): RedirectResponse
    {
        $this->authorize('resend', $invitation);

        if ($invitation->business_id !== $business->id) {
            abort(404);
        }

        // Cancel old invitation and create new one
        $invitation->update(['status' => 'cancelled']);

        try {
            $inviteAction->execute($business, $invitation->email, $invitation->role);

            return back()->with('success', 'Invitation resent successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
