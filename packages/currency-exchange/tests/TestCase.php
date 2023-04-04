<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Petshop\CurrencyExchange\CurrencyExchangeServiceProvider;

class TestCase extends Orchestra
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            CurrencyExchangeServiceProvider::class,
        ];
    }
}
