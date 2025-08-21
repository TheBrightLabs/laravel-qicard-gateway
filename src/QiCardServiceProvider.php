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
        $this->publishes([
            __DIR__ . '/../config/qi_card.php' => config_path('qi_card.php'),
        ], 'config');
    }
}
