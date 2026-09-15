<?php

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google'])
    ->name('auth.social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google'])
    ->name('auth.social.callback');

// Catch-all: hand every non-API, non-Sanctum route to the Vue SPA so
// client-side routing (Vue Router) can take over.
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|sanctum|broadcasting|auth).*$')->name('spa');
