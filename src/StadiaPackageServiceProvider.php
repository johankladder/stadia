<?php

namespace JohanKladder\Stadia;

use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use JohanKladder\Stadia\Charts\HarvestChart;
use JohanKladder\Stadia\Charts\LevelChart;

class StadiaPackageServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('stadia', function ($app) {
            return new Stadia();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'stadia');
    }

    public function boot(Charts $charts)
    {
        $charts->register([
            HarvestChart::class,
            LevelChart::class,
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'stadia');
        $this->publishResources();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('stadia.php'),
            ], 'config');

        }
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        Route::group($this->routeConfigurationApi(), function () {

            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('stadia.prefix'),
            'middleware' => config('stadia.middleware'),
        ];
    }

    protected function routeConfigurationApi()
    {
        return [
            'prefix' => config('stadia.api-prefix'),
            'middleware' => config('stadia.api-middleware'),
        ];
    }

    protected function publishResources()
    {
        $this->publishes([
            __DIR__ . '/../database/seeds/CountriesTableSeeder.php' => database_path('seeds/CountriesTableSeeder.php'),
            __DIR__ . '/../database/seeds/ClimateCodesTableSeeder.php' => database_path('seeds/ClimateCodesTableSeeder.php'),
            __DIR__ . '/../database/seeds/KoepenLocationTableSeeder.php' => database_path('seeds/KoepenLocationTableSeeder.php'),
        ], 'stadia-seeds');

        $this->publishes([
            __DIR__ . '/../database/seeds/datasets/' => database_path('seeds/datasets')
        ], 'stadia-datasets');
    }

}
