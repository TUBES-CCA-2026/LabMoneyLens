<?php

namespace App\Providers;

<<<<<<< HEAD
=======
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
>>>>>>> 0026227 (Baru)
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
<<<<<<< HEAD
    /**
     * Register any application services.
     */
=======
>>>>>>> 0026227 (Baru)
    public function register(): void
    {
        //
    }

<<<<<<< HEAD
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
=======
    public function boot(): void
    {
        RateLimiter::for('login', function ($request) {
            $identifier = strtolower(trim((string) $request->input('identifier')));

            return Limit::perMinute(5)->by($identifier.'|'.$request->ip());
        });
>>>>>>> 0026227 (Baru)
    }
}
