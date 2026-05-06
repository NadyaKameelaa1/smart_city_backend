<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});


// Redirect user ke halaman login SSO
Route::get('/auth/sso/redirect', function () {
    return Socialite::driver('sso')->redirect();
});

// Callback setelah user login di SSO
Route::get('/auth/sso/callback', function () {
    try {
        $ssoUser = Socialite::driver('sso')->stateless()->user();

        $email = $ssoUser->getEmail();
        $user = \App\Models\User::where('email', $email)->first();

        if (! $user) {
            $baseUsername = Str::slug(Str::before($email, '@')) ?: 'user';
            $username = $baseUsername;
            $suffix = 1;

            while (\App\Models\User::where('username', $username)->exists()) {
                $username = $baseUsername . '-' . $suffix;
                $suffix++;
            }

            $user = \App\Models\User::create([
                'username' => $username,
                'name'     => $ssoUser->getName() ?: $username,
                'email'    => $email,
                'password' => Hash::make(Str::random(64)),
                'sso_id'   => $ssoUser->getId(),
            ]);
        } else {
            $user->name = $ssoUser->getName() ?: $user->name;
            $user->sso_id = $ssoUser->getId();
            $user->save();
        }

        // Buat token Sanctum untuk user ini
        $token = $user->createToken('sso-token')->plainTextToken;

        // Kirim token ke frontend via query string
        // Frontend akan ambil token ini dan simpan ke localStorage
        $frontendUrl = rtrim(config('services.sso.frontend_url', 'https://sso.qode.my.id'), '/');

        return redirect($frontendUrl . '/sso-callback?' . http_build_query([
            'token' => $token,
        ]));

    } catch (\Throwable $e) {
        \Log::error('Smart City SSO callback failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $frontendUrl = rtrim(config('services.sso.frontend_url', 'https://sso.qode.my.id'), '/');

        return redirect($frontendUrl . '/login?error=sso_failed');
    }
});
