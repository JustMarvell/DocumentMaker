<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Source - https://stackoverflow.com/a/75049850
        // Posted by Murtaza Noori, modified by community. See post 'Timeline' for change history
        // Retrieved 2026-04-21, License - CC BY-SA 4.0

        if (env(key: 'APP_ENV') !== 'local') {
            URL::forceScheme(scheme: 'https');
        }

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
