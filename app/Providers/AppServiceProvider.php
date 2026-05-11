<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\DB::connection()->getSchemaBuilder()
            ->defaultStringLength(191);

        Paginator::useTailwind();

        if (request()->getHost() !== '127.0.0.1' && request()->getHost() !== 'localhost') {
            URL::forceScheme('https');
        }

        // Force the URL root to APP_URL so generated links (including emails)
        // always use the correct host — important when running behind ngrok or a proxy
        // $appUrl = config('app.url');

        // if ($appUrl && $appUrl !== 'http://localhost') {
        //     URL::forceRootUrl($appUrl);
        // }

        // if (str_starts_with($appUrl, 'https://')) {
        //     URL::forceScheme('https');
        // }
    }
}
