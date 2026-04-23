<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

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
        Paginator::useBootstrapFive();

        // Register Livewire update route with proper tenancy initialization.
        // This ensures that for tenant domains, the tenant DB is switched BEFORE
        // the session is started (web middleware), so auth()->check() works correctly.
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware([
                    \App\Http\Middleware\InitializeTenancyForLivewire::class,
                    'web',
                ]);
        });
    }
}
