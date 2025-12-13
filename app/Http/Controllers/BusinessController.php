<?php

namespace App\Http\Controllers;

use App\Actions\Business\CreateBusiness;
use App\Actions\Business\DeleteBusiness;
use App\Actions\Business\UpdateBusiness;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use App\Models\Business;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessController extends Controller
{
    /**
     * Display a listing of the user's businesses.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Business::class);

        $businesses = auth()->user()->businesses()
            ->with('plan')
            ->withPivot('role')
            ->get();

        return view('businesses.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create(): View
    {
        $this->authorize('create', Business::class);

        $plans = Plan::all();

        return view('businesses.create', compact('plans'));
    }

    /**
     * Store a newly created business.
     */
    public function store(StoreBusinessRequest $request, CreateBusiness $action): RedirectResponse
    {
        $this->authorize('create', Business::class);

        $business = $action->execute(auth()->user(), $request->validated());

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', 'Business created successfully!');
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['plan', 'users', 'projects']);

        return view('businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the business.
     */
    public function edit(Business $business): View
    {
        $this->authorize('update', $business);

        return view('businesses.edit', compact('business'));
    }

    /**
     * Update the specified business.
     */
    public function update(UpdateBusinessRequest $request, Business $business, UpdateBusiness $action): RedirectResponse
    {
        $this->authorize('update', $business);

        $action->execute($business, $request->validated());

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', 'Business updated successfully!');
    }

    /**
     * Remove the specified business.
     */
    public function destroy(Business $business, DeleteBusiness $action): RedirectResponse
    {
        $this->authorize('delete', $business);

        $action->execute($business, auth()->user());

        return redirect()
            ->route('businesses.index')
            ->with('success', 'Business deleted successfully!');
    }

    /**
     * Switch the current business context.
     */
    public function switch(Business $business): RedirectResponse
    {
        $this->authorize('view', $business);

        session(['current_business_id' => $business->id]);

        return redirect()
            ->back()
            ->with('success', "Switched to {$business->name}");
    }

    /**
     * Show team management page.
     */
    public function team(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['users', 'plan']);

        $pendingInvitations = $business->invitations()
            ->where('status', 'pending')
            ->with('inviter')
            ->latest()
            ->get();

        return view('businesses.team', compact('business', 'pendingInvitations'));
    }
}
