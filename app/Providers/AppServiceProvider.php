<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
        // Paginator::useBootstrapFive();

        // The site has no Tailwind/Bootstrap, so Laravel's default pagination
        // renders huge arrows. Use our self-styled view for every table.
        Paginator::defaultView('partials.pagination');
    }
}
