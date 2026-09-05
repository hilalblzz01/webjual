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
        // Bind public path to base_path if extracted directly in public_html without public folder
        if (file_exists(base_path('index.php')) && !file_exists(base_path('../public_html'))) {
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
