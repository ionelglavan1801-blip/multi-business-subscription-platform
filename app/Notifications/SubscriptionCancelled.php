<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Business $business,
        public ?string $planName = null
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
        $pricingUrl = url('/');

        return (new MailMessage)
            ->subject('Subscription Cancelled - '.$this->business->name)
            ->greeting('Hello!')
            ->line('Your subscription for **'.$this->business->name.'** has been cancelled.')
            ->line('Your business has been moved to the Free plan with limited features.')
            ->line('You can resubscribe at any time to regain access to premium features.')
            ->action('View Pricing Plans', $pricingUrl)
            ->line('We\'re sorry to see you go. If you have any feedback, please let us know.');
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
            'previous_plan' => $this->planName,
        ];
    }
}
