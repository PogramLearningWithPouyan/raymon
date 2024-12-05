<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
    public function map()
    {
        $this->mapApiRoutes();

        // Other route registration...
    }

    protected function mapApiRoutes()
    {
//        Route::prefix('api')  // Routes will be prefixed with `/api`
//        ->middleware('api')  // Uses the 'api' middleware group
//        ->namespace($this->namespace)
//            ->group(base_path('routes'));
    }
}
