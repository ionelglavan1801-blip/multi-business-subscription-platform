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
        $plans = Plan::all();

        return view('businesses.create', compact('plans'));
    }

    /**
     * Store a newly created business.
     */
    public function store(StoreBusinessRequest $request, CreateBusiness $action): RedirectResponse
    {
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
        $business->load(['plan', 'users', 'projects']);

        return view('businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the business.
     */
    public function edit(Business $business): View
    {
        return view('businesses.edit', compact('business'));
    }

    /**
     * Update the specified business.
     */
    public function update(UpdateBusinessRequest $request, Business $business, UpdateBusiness $action): RedirectResponse
    {
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
        $action->execute($business);

        return redirect()
            ->route('businesses.index')
            ->with('success', 'Business deleted successfully!');
    }
}
