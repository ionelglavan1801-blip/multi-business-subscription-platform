<?php

namespace App\Http\Controllers;

use App\Actions\Billing\CreateCheckoutSession;
use App\Models\Business;
use App\Models\Plan;
use App\Services\Billing\StripeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected StripeService $stripe,
        protected CreateCheckoutSession $createCheckout
    ) {}

    /**
     * Show billing page for business
     */
    public function index(Business $business): View
    {
        $this->authorize('manageBilling', $business);

        $business->load(['plan', 'activeSubscription']);

        $plans = Plan::orderBy('price_monthly')->get();

        return view('billing.index', compact('business', 'plans'));
    }

    /**
     * Create checkout session
     */
    public function checkout(Request $request, Business $business): RedirectResponse
    {
        $this->authorize('manageBilling', $business);

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        try {
            $url = $this->createCheckout->execute($business, $plan);

            return redirect($url);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Checkout success callback
     */
    public function success(Business $business): RedirectResponse
    {
        return redirect()->route('businesses.show', $business)
            ->with('success', 'Subscription activated! Welcome to '.$business->plan->name.' plan.');
    }

    /**
     * Checkout cancel callback
     */
    public function cancel(Business $business): RedirectResponse
    {
        return redirect()->route('billing.index', $business)
            ->with('info', 'Checkout cancelled. You can try again anytime.');
    }

    /**
     * Open customer portal
     */
    public function portal(Business $business): RedirectResponse
    {
        $this->authorize('manageBilling', $business);

        $returnUrl = route('billing.index', $business);
        $session = $this->stripe->createPortalSession(auth()->user(), $returnUrl);

        return redirect($session->url);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Business $business): RedirectResponse
    {
        $this->authorize('manageBilling', $business);

        $subscription = $business->activeSubscription;

        if (! $subscription) {
            return back()->withErrors(['error' => 'No active subscription found.']);
        }

        try {
            $this->stripe->cancelSubscription($subscription->stripe_subscription_id);

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel subscription: '.$e->getMessage()]);
        }
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(Business $business): RedirectResponse
    {
        $this->authorize('manageBilling', $business);

        $subscription = $business->activeSubscription;

        if (! $subscription) {
            return back()->withErrors(['error' => 'No active subscription found.']);
        }

        try {
            $this->stripe->resumeSubscription($subscription->stripe_subscription_id);

            return back()->with('success', 'Subscription resumed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to resume subscription: '.$e->getMessage()]);
        }
    }
}
