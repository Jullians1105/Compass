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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // La app no carga Tailwind (ver app.scss): las vistas paginadas
        // (RF-08) necesitan los links de paginacion en Bootstrap 5.
        Paginator::useBootstrapFive();
    }
}
