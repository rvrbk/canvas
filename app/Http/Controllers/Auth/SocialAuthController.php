<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Throwable;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google'];

    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        return $driver->stateless()->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            $socialUser = $driver->stateless()->user();
        } catch (Throwable) {
            return redirect('/login?social_error=auth_failed');
        }

        $email = trim((string) $socialUser->getEmail());
        if ($email === '') {
            return redirect('/login?social_error=email_required');
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => trim((string) ($socialUser->getName() ?: $socialUser->getNickname() ?: 'Artist')),
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
                'oauth_provider' => $provider,
                'oauth_provider_id' => (string) $socialUser->getId(),
            ]);
        } else {
            $user->forceFill([
                'oauth_provider' => $provider,
                'oauth_provider_id' => (string) $socialUser->getId(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect('/');
    }
}
