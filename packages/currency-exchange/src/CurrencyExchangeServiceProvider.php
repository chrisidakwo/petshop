<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class CurrencyExchangeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/currency-exchange.php', 'currency-exchange',
        );

        $this->app->bind(
            \Petshop\CurrencyExchange\Contracts\CurrencyExchange::class,
            function (Application $app) {
                return new CurrencyExchange(
                    app: $app,
                    providers: $app->make('config')->get('currency-exchange.exchange_providers'),
                );
            }
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/currency-exchange.php' => config_path('currency_exchange.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes/route.php');
    }
}
