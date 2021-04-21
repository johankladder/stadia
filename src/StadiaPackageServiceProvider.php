<?php

namespace JohanKladder\Stadia;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StadiaPackageServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('stadia', function ($app) {
            return new Stadia();
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'stadia');
        $this->publishResources();

    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => 'stadia',
            'middleware' => ['web'],
        ];
    }

    protected function publishResources()
    {
        $this->publishes([
            __DIR__ . '/../database/seeds/CountriesTableSeeder.php' => database_path('seeds/CountriesTableSeeder.php'),
        ], 'stadia-country-seeds');
    }

}
