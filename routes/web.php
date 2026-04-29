<?php

use Illuminate\Support\Facades\Route;
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
        $ssoUser = Socialite::driver('sso')->user();

        $user = \App\Models\User::updateOrCreate(
            ['email' => $ssoUser->getEmail()],
            [
                'name'   => $ssoUser->getName(),
                'sso_id' => $ssoUser->getId(),
            ]
        );

        // Buat token Sanctum untuk user ini
        $token = $user->createToken('sso-token')->plainTextToken;

        // Kirim token ke frontend via query string
        // Frontend akan ambil token ini dan simpan ke localStorage
        $frontendUrl = rtrim(config('services.sso.frontend_url', 'http://41.216.191.37:5173'), '/');

        return redirect($frontendUrl . '/sso-callback?token=' . $token);

    } catch (\Throwable $e) {
        $frontendUrl = rtrim(config('services.sso.frontend_url', 'http://41.216.191.37:5173'), '/');

        return redirect($frontendUrl . '/login?error=sso_failed');
    }
});
