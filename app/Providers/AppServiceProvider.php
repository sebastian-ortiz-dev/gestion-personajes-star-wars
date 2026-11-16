<?php

namespace App\Providers;

use App\Repositories\Eloquent\PersonajeRepository;
use App\Repositories\PersonajeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PersonajeRepositoryInterface::class,
            PersonajeRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
