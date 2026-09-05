<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Support public_html folder on both localhost and cPanel seamlessly
        if (file_exists(base_path('public_html'))) {
            $this->app->usePublicPath(base_path('public_html'));
        } elseif (file_exists(base_path('index.php')) && !file_exists(base_path('public'))) {
            $this->app->usePublicPath(base_path());
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
    }
}
