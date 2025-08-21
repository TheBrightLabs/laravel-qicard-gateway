<?php

namespace Thebrightlabs\IraqPayments;

use Illuminate\Support\ServiceProvider;
class QiCardServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/qi_card.php', 'qi_card'
        );
        $this->app->singleton(QiCardGateway::class,function ($app){
            return new QiCardGateway();
        });
    }

    public function boot()
    {
        // load package config
        $this->publishes([
            __DIR__ . '/../config/qi_card.php' => config_path('qi_card.php'),
        ], 'config');

        // Allow publishing migrations to the main app
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        //  publish seeders
        $this->publishes([
            __DIR__.'/../database/seeders/' => database_path('seeders'),
        ], 'seeders');

        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
