<?php

namespace App\Notifications;

use App\Models\Business;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Business $business,
        public Plan $plan
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
        $dashboardUrl = route('businesses.show', $this->business);

        return (new MailMessage)
            ->subject('🎉 Subscription Activated - '.$this->business->name)
            ->greeting('Great news!')
            ->line('Your subscription to the **'.$this->plan->name.'** plan has been successfully activated for '.$this->business->name.'.')
            ->line('You now have access to:')
            ->line('• Up to '.$this->plan->max_users_per_business.' team members')
            ->line('• Up to '.$this->plan->max_projects.' projects')
            ->line('• All '.$this->plan->name.' features')
            ->action('Go to Dashboard', $dashboardUrl)
            ->line('Thank you for choosing MultiApp!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
            'plan_id' => $this->plan->id,
            'plan_name' => $this->plan->name,
        ];
    }
}
