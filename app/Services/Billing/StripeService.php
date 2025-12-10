<?php

namespace App\Services\Billing;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Subscription;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create or retrieve Stripe customer for user
     */
    public function createOrGetCustomer(User $user): Customer
    {
        if ($user->stripe_customer_id) {
            try {
                return Customer::retrieve($user->stripe_customer_id);
            } catch (ApiErrorException $e) {
                // Customer doesn't exist, create new one
            }
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    /**
     * Create checkout session for subscription
     */
    public function createCheckoutSession(
        Business $business,
        Plan $plan,
        string $successUrl,
        string $cancelUrl
    ): Session {
        $owner = $business->owner;
        $customer = $this->createOrGetCustomer($owner);

        return Session::create([
            'customer' => $customer->id,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'business_id' => $business->id,
                'plan_id' => $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'business_id' => $business->id,
                    'plan_id' => $plan->id,
                ],
            ],
        ]);
    }

    /**
     * Create customer portal session
     */
    public function createPortalSession(User $user, string $returnUrl): \Stripe\BillingPortal\Session
    {
        $customer = $this->createOrGetCustomer($user);

        return \Stripe\BillingPortal\Session::create([
            'customer' => $customer->id,
            'return_url' => $returnUrl,
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(string $stripeSubscriptionId): Subscription
    {
        return Subscription::update($stripeSubscriptionId, [
            'cancel_at_period_end' => true,
        ]);
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(string $stripeSubscriptionId): Subscription
    {
        return Subscription::update($stripeSubscriptionId, [
            'cancel_at_period_end' => false,
        ]);
    }

    /**
     * Get subscription details
     */
    public function getSubscription(string $stripeSubscriptionId): ?Subscription
    {
        try {
            return Subscription::retrieve($stripeSubscriptionId);
        } catch (ApiErrorException $e) {
            return null;
        }
    }
}
