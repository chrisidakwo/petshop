<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PetShop\CurrencyExchange\Cache\Repository;
use PetShop\CurrencyExchange\Contracts\Cache;

class CurrencyExchangeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/currency-exchange.php', 'currency-exchange',
        );

        $this->app->bind(Cache::class, Repository::class);

        $this->app->instance(CurrencyExchange::class, function (Application $app) {
            return new CurrencyExchange(
                cache: $app->make(Cache::class),
                app: $app,
                providers: $app->make('config')->get('currency-exchange.exchange_providers'),
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/currency-exchange.php' => config_path('currency_exchange.php'),
        ], 'config');
    }
}
