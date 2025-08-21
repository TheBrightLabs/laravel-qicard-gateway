<?php

namespace Thebrightlabs\IraqPayments;

use Illuminate\Support\ServiceProvider;
class QiCardServiceProvider
{

    public function register()
    {
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
