<?php

namespace App\Actions\Billing;

use App\Models\Business;
use App\Models\Plan;
use App\Models\StripeWebhookLog;
use App\Models\Subscription;
use App\Notifications\PaymentFailed;
use App\Notifications\SubscriptionActivated;
use App\Notifications\SubscriptionCancelled;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HandleStripeWebhook
{
    public function execute(array $payload): void
    {
        $eventType = $payload['type'];
        $data = $payload['data']['object'];

        // Log webhook
        StripeWebhookLog::create([
            'event_id' => $payload['id'],
            'type' => $eventType,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            match ($eventType) {
                'checkout.session.completed' => $this->handleCheckoutCompleted($data),
                'customer.subscription.created' => $this->handleSubscriptionCreated($data),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($data),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($data),
                'invoice.payment_succeeded' => $this->handlePaymentSucceeded($data),
                'invoice.payment_failed' => $this->handlePaymentFailed($data),
                default => Log::info("Unhandled webhook: {$eventType}"),
            };

            // Mark as processed
            StripeWebhookLog::where('event_id', $payload['id'])
                ->update(['status' => 'processed']);
        } catch (\Exception $e) {
            Log::error("Webhook processing failed: {$eventType}", [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            StripeWebhookLog::where('event_id', $payload['id'])
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

            throw $e;
        }
    }

    protected function handleCheckoutCompleted(array $data): void
    {
        $businessId = $data['metadata']['business_id'] ?? null;
        $planId = $data['metadata']['plan_id'] ?? null;

        if (! $businessId || ! $planId) {
            Log::warning('Checkout completed without business/plan metadata');

            return;
        }

        $business = Business::find($businessId);
        $plan = Plan::find($planId);

        if (! $business || ! $plan) {
            Log::warning('Business or plan not found', [
                'business_id' => $businessId,
                'plan_id' => $planId,
            ]);

            return;
        }

        // Subscription will be created by subscription.created event
        Log::info('Checkout completed', [
            'business_id' => $businessId,
            'plan_id' => $planId,
        ]);
    }

    protected function handleSubscriptionCreated(array $data): void
    {
        $businessId = $data['metadata']['business_id'] ?? null;
        $planId = $data['metadata']['plan_id'] ?? null;

        if (! $businessId || ! $planId) {
            Log::warning('Subscription created without metadata');

            return;
        }

        $business = Business::find($businessId);
        $plan = Plan::find($planId);

        if (! $business || ! $plan) {
            return;
        }

        // Extract period dates from subscription items (new Stripe API structure)
        $periodStart = $this->extractPeriodStart($data);
        $periodEnd = $this->extractPeriodEnd($data);

        Subscription::create([
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'stripe_customer_id' => $data['customer'],
            'stripe_subscription_id' => $data['id'],
            'status' => $data['status'],
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
            'trial_ends_at' => isset($data['trial_end']) ? Carbon::createFromTimestamp($data['trial_end']) : null,
        ]);

        // Update business plan
        $business->update(['plan_id' => $plan->id]);

        // Notify business owner
        $owner = $business->owner;
        if ($owner) {
            $owner->notify(new SubscriptionActivated($business, $plan));
        }

        Log::info('Subscription created', [
            'business_id' => $businessId,
            'subscription_id' => $data['id'],
        ]);
    }

    protected function handleSubscriptionUpdated(array $data): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $data['id'])->first();

        if (! $subscription) {
            Log::warning('Subscription not found for update', ['stripe_id' => $data['id']]);

            return;
        }

        // Extract period dates from subscription items (new Stripe API structure)
        $periodStart = $this->extractPeriodStart($data);
        $periodEnd = $this->extractPeriodEnd($data);

        $subscription->update([
            'status' => $data['status'],
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
            'ends_at' => $data['cancel_at_period_end']
                ? $periodEnd
                : null,
        ]);

        Log::info('Subscription updated', [
            'subscription_id' => $data['id'],
            'status' => $data['status'],
        ]);
    }

    protected function handleSubscriptionDeleted(array $data): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $data['id'])->first();

        if (! $subscription) {
            return;
        }

        $business = $subscription->business;
        $previousPlanName = $subscription->plan?->name;

        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);

        // Revert to free plan
        $freePlan = Plan::where('slug', 'free')->first();
        if ($freePlan) {
            $business->update(['plan_id' => $freePlan->id]);
        }

        // Notify business owner
        $owner = $business->owner;
        if ($owner) {
            $owner->notify(new SubscriptionCancelled($business, $previousPlanName));
        }

        Log::info('Subscription deleted', [
            'subscription_id' => $data['id'],
            'business_id' => $subscription->business_id,
        ]);
    }

    protected function handlePaymentSucceeded(array $data): void
    {
        if (! isset($data['subscription'])) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $data['subscription'])->first();

        if (! $subscription) {
            return;
        }

        // Update status if needed
        if ($subscription->status !== 'active') {
            $subscription->update(['status' => 'active']);
        }

        Log::info('Payment succeeded', [
            'subscription_id' => $data['subscription'],
            'amount' => $data['amount_paid'],
        ]);
    }

    protected function handlePaymentFailed(array $data): void
    {
        if (! isset($data['subscription'])) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $data['subscription'])->first();

        if (! $subscription) {
            return;
        }

        $subscription->update(['status' => 'past_due']);

        Log::warning('Payment failed', [
            'subscription_id' => $data['subscription'],
            'business_id' => $subscription->business_id,
        ]);

        // Notify business owner
        $owner = $subscription->business->owner;
        if ($owner) {
            $amountCents = $data['amount_due'] ?? null;
            $owner->notify(new PaymentFailed($subscription->business, $amountCents));
        }
    }

    /**
     * Extract period start from subscription data (handles new Stripe API structure).
     */
    protected function extractPeriodStart(array $data): Carbon
    {
        // Try new API structure: items.data[0].current_period_start
        if (isset($data['items']['data'][0]['current_period_start'])) {
            return Carbon::createFromTimestamp($data['items']['data'][0]['current_period_start']);
        }

        // Fallback to old structure
        if (isset($data['current_period_start'])) {
            return Carbon::createFromTimestamp($data['current_period_start']);
        }

        // Default to start_date or now
        return isset($data['start_date'])
            ? Carbon::createFromTimestamp($data['start_date'])
            : now();
    }

    /**
     * Extract period end from subscription data (handles new Stripe API structure).
     */
    protected function extractPeriodEnd(array $data): Carbon
    {
        // Try new API structure: items.data[0].current_period_end
        if (isset($data['items']['data'][0]['current_period_end'])) {
            return Carbon::createFromTimestamp($data['items']['data'][0]['current_period_end']);
        }

        // Fallback to old structure
        if (isset($data['current_period_end'])) {
            return Carbon::createFromTimestamp($data['current_period_end']);
        }

        // Default to 30 days from now
        return now()->addDays(30);
    }
}
