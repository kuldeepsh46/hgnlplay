<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymentDueService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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

        // Payment due alert + sidebar badge for members on every dashboard page
        View::composer('common.layout', function ($view) {
            $user = Auth::user();
            $view->with('paymentDue', $user && $user->hasRole('customer')
                ? PaymentDueService::forUser($user)
                : null);
        });
    }
}
