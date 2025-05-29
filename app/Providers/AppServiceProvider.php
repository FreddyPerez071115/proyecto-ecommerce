<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\URL; // Importar el Facade URL

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificacionService::class, function ($app) {
            return new NotificacionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Forzar HTTPS en producción si es necesario
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
