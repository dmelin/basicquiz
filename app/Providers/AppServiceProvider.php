<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GameService;
use App\Services\NumbersApiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GameService::class, function ($app) {
            return new GameService();
        });

        $this->app->singleton(NumbersApiService::class, function ($app) {
            return new NumbersApiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
