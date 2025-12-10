<?php

namespace App\Actions\Billing;

use App\Models\Business;
use App\Models\Plan;
use App\Services\Billing\StripeService;
use Illuminate\Validation\ValidationException;

class CreateCheckoutSession
{
    public function __construct(
        protected StripeService $stripe
    ) {}

    public function execute(Business $business, Plan $plan): string
    {
        // Validate plan has Stripe price ID
        if (! $plan->stripe_price_id) {
            throw ValidationException::withMessages([
                'plan' => 'This plan does not support online payments.',
            ]);
        }

        // Validate business owner
        if (! $business->owner) {
            throw ValidationException::withMessages([
                'business' => 'Business must have an owner.',
            ]);
        }

        $successUrl = route('billing.success', [
            'business' => $business->id,
        ]);

        $cancelUrl = route('billing.cancel', [
            'business' => $business->id,
        ]);

        $session = $this->stripe->createCheckoutSession(
            $business,
            $plan,
            $successUrl,
            $cancelUrl
        );

        return $session->url;
    }
}
