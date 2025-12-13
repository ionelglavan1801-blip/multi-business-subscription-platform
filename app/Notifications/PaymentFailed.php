<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Business $business,
        public ?int $amountCents = null
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
        $billingUrl = route('billing.index', $this->business);
        $amount = $this->amountCents ? number_format($this->amountCents / 100, 2) : null;

        $message = (new MailMessage)
            ->subject('⚠️ Payment Failed - '.$this->business->name)
            ->greeting('Action Required')
            ->line('We were unable to process your payment for **'.$this->business->name.'**.');

        if ($amount) {
            $message->line('Amount: $'.$amount);
        }

        return $message
            ->line('Please update your payment information to avoid service interruption.')
            ->line('If your payment method is not updated, your subscription may be suspended.')
            ->action('Update Payment Method', $billingUrl)
            ->line('If you need assistance, please contact our support team.');
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
            'amount_cents' => $this->amountCents,
        ];
    }
}
