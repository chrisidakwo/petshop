<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Providers;

use Illuminate\Contracts\Foundation\Application;
use Petshop\CurrencyExchange\Contracts\ExchangeProvider;

abstract class AbstractExchangeProvider implements ExchangeProvider
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
