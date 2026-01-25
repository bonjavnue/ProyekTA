<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\RouteHelper;
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
        // Share breadcrumb and page title with all views
        View::composer('*', function ($view) {
            $view->with('breadcrumbs', RouteHelper::getBreadcrumbs());
            $view->with('pageTitle', RouteHelper::getPageTitle());
        });
    }
}
