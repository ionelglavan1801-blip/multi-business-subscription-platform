<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $businessName,
        public string $deletedByName
    ) {
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
        $dashboardUrl = route('dashboard');

        return (new MailMessage)
            ->subject('Business Deleted - '.$this->businessName)
            ->greeting('Hello!')
            ->line('The business **'.$this->businessName.'** has been deleted by '.$this->deletedByName.'.')
            ->line('All associated data, projects, and subscriptions have been removed.')
            ->line('If this was done in error, please contact our support team immediately.')
            ->action('Go to Dashboard', $dashboardUrl)
            ->line('Thank you for using MultiApp.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'business_name' => $this->businessName,
            'deleted_by' => $this->deletedByName,
        ];
    }
}
