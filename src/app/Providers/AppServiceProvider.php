<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        if (config('app.env') === 'production') {
            Url::forceScheme('https');
        }

        Inertia::share('flash', function () {
                    return [
                        'message' => session('message'),
                        'error' => session('error'),
                    ];
        });
    }
}
