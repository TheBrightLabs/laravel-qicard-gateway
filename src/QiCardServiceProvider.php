<?php

namespace Thebrightlabs\QiCard;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TheBrightLabs\QiCard\Console\Commands\CheckPaymentStatuses;

class QiCardServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/qi_card.php', 'qi_card'
        );
        $this->app->singleton(QiCardGateway::class, function ($app) {
            return new QiCardGateway();
        });


    }

    public function boot()
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckPaymentStatuses::class,
            ]);
        }
        // Load package things
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
        // publishes
        $this->publishes([
            __DIR__ . '/../config/qi_card.php' => config_path('qi_card.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

    }
}
