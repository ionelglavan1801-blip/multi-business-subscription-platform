<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Business routes
    Route::resource('businesses', \App\Http\Controllers\BusinessController::class);
    Route::post('/businesses/{business}/switch', [\App\Http\Controllers\BusinessController::class, 'switch'])->name('businesses.switch');
    Route::get('/businesses/{business}/team', [\App\Http\Controllers\BusinessController::class, 'team'])->name('businesses.team');

    // Invitation routes (within business context)
    Route::post('/businesses/{business}/invitations', [\App\Http\Controllers\InvitationController::class, 'store'])->name('businesses.invitations.store');
    Route::delete('/businesses/{business}/invitations/{invitation}', [\App\Http\Controllers\InvitationController::class, 'destroy'])->name('businesses.invitations.destroy');
    Route::post('/businesses/{business}/invitations/{invitation}/resend', [\App\Http\Controllers\InvitationController::class, 'resend'])->name('businesses.invitations.resend');
});

// Public invitation routes (no auth required for viewing)
Route::get('/invitations/{token}', [\App\Http\Controllers\InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept', [\App\Http\Controllers\InvitationController::class, 'accept'])->middleware('auth')->name('invitations.accept');

require __DIR__.'/auth.php';
