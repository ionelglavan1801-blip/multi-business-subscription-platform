<?php

use App\Http\Controllers\ProfileController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = Plan::orderBy('price_monthly')->get();

    return view('welcome', compact('plans'));
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $currentBusiness = $user->currentBusiness();

    return view('dashboard', [
        'user' => $user,
        'currentBusiness' => $currentBusiness,
        'businessCount' => $user->businesses()->count(),
        'teamMemberCount' => $currentBusiness?->users()->count() ?? 0,
        'projectCount' => $currentBusiness?->projects()->count() ?? 0,
        'subscription' => $currentBusiness?->subscription,
        'plan' => $currentBusiness?->plan,
        'businesses' => $user->businesses()->with('plan')->latest()->take(5)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Business routes
    Route::resource('businesses', \App\Http\Controllers\BusinessController::class);
    Route::post('/businesses/{business}/switch', [\App\Http\Controllers\BusinessController::class, 'switch'])->name('businesses.switch');
    Route::get('/businesses/{business}/team', [\App\Http\Controllers\BusinessController::class, 'team'])->name('businesses.team');

    // Project routes (nested under businesses)
    Route::resource('businesses.projects', \App\Http\Controllers\ProjectController::class);

    // Invitation routes (within business context)
    Route::post('/businesses/{business}/invitations', [\App\Http\Controllers\InvitationController::class, 'store'])->name('businesses.invitations.store');
    Route::delete('/businesses/{business}/invitations/{invitation}', [\App\Http\Controllers\InvitationController::class, 'destroy'])->name('businesses.invitations.destroy');
    Route::post('/businesses/{business}/invitations/{invitation}/resend', [\App\Http\Controllers\InvitationController::class, 'resend'])->name('businesses.invitations.resend');

    // Billing routes
    Route::get('/businesses/{business}/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::post('/businesses/{business}/billing/checkout', [\App\Http\Controllers\BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/businesses/{business}/billing/success', [\App\Http\Controllers\BillingController::class, 'success'])->name('billing.success');
    Route::get('/businesses/{business}/billing/cancel', [\App\Http\Controllers\BillingController::class, 'cancel'])->name('billing.cancel');
    Route::get('/businesses/{business}/billing/portal', [\App\Http\Controllers\BillingController::class, 'portal'])->name('billing.portal');
    Route::post('/businesses/{business}/billing/cancel-subscription', [\App\Http\Controllers\BillingController::class, 'cancelSubscription'])->name('billing.cancel-subscription');
    Route::post('/businesses/{business}/billing/resume-subscription', [\App\Http\Controllers\BillingController::class, 'resumeSubscription'])->name('billing.resume-subscription');
});

// Public invitation routes (no auth required for viewing)
Route::get('/invitations/{token}', [\App\Http\Controllers\InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept', [\App\Http\Controllers\InvitationController::class, 'accept'])->middleware('auth')->name('invitations.accept');

// Stripe webhook (no CSRF protection)
Route::post('/webhook/stripe', \App\Http\Controllers\StripeWebhookController::class)->name('webhook.stripe');

require __DIR__.'/auth.php';
