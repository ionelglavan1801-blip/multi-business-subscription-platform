<?php

namespace App\Http\Middleware;

use App\Models\Business;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentBusiness
{
    /**
     * Routes that don't require a business context.
     */
    protected array $excludedRoutes = [
        'businesses.create',
        'businesses.store',
        'profile.edit',
        'profile.update',
        'profile.destroy',
        'password.confirm',
        'password.update',
        'verification.notice',
        'verification.verify',
        'verification.send',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        // Skip middleware for excluded routes
        foreach ($this->excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        $user = $request->user();
        $currentBusinessId = session('current_business_id');

        // If no business is selected in session
        if (! $currentBusinessId) {
            // Get user's first business
            $firstBusiness = $user->businesses()->first();

            if ($firstBusiness) {
                session(['current_business_id' => $firstBusiness->id]);
                $currentBusinessId = $firstBusiness->id;
            } else {
                // User has no businesses - redirect to create one
                return redirect()->route('businesses.create')
                    ->with('info', 'Please create your first business to continue.');
            }
        }

        // Verify user has access to selected business
        if ($currentBusinessId) {
            $business = Business::find($currentBusinessId);

            if (! $business || ! $user->businesses()->where('business_id', $business->id)->exists()) {
                // User doesn't have access to this business - reset to first business
                $firstBusiness = $user->businesses()->first();

                if ($firstBusiness) {
                    session(['current_business_id' => $firstBusiness->id]);
                } else {
                    session()->forget('current_business_id');

                    return redirect()->route('businesses.create')
                        ->with('info', 'Please create your first business to continue.');
                }
            }
        }

        return $next($request);
    }
}
