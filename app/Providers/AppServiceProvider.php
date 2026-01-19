<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Принудительно используем HTTPS в продакшене
        if ($this->app->environment('production') || 
            config('app.force_https') === true) {
            URL::forceScheme('https');
        }
    }
}
