<?php

namespace App\Providers;

use App\Socialite\SsoProvider; 
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Socialite::extend('sso', function ($app) {
            $config = $app['config']['services.sso'];

            return Socialite::buildProvider(SsoProvider::class, $config);
        });
    }
}
