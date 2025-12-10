<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedToBusiness extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Invitation $invitation)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('invitations.accept', ['token' => $this->invitation->token]);

        return (new MailMessage)
            ->subject('Invitation to join '.$this->invitation->business->name)
            ->greeting('Hello!')
            ->line($this->invitation->inviter->name.' has invited you to join '.$this->invitation->business->name.' as a '.$this->invitation->role.'.')
            ->line('This invitation will expire on '.$this->invitation->expires_at->format('F j, Y \a\t g:i A').'.')
            ->action('Accept Invitation', $acceptUrl)
            ->line('If you did not expect this invitation, you can safely ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'business_id' => $this->invitation->business_id,
            'business_name' => $this->invitation->business->name,
            'role' => $this->invitation->role,
            'invited_by' => $this->invitation->inviter->name,
        ];
    }
}
