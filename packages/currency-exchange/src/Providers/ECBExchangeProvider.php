<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Petshop\CurrencyExchange\ExchangeRate;

class ECBExchangeProvider extends AbstractExchangeProvider
{
    /**
     * @inheritDoc
     */
    public function getSourceCurrency(): string
    {
        return 'EUR';
    }

    /**
     * {@inheritdoc}
     */
    public function getRates(string $date, int $fallBackPeriod = 7): array
    {
        // TODO: Implement getRates() method.
    }

    /**
     * {@inheritdoc}
     */
    public function convert(string $destCurrency, string $date = null): ExchangeRate
    {
        // TODO: Implement convert() method.
    }

    /**
     * @inheritDoc
     *
     * @throws BindingResolutionException
     */
    public function getSupportedCurrencies(): array
    {
        return $this->app->make('config')->get('currency-exchange.supported_currencies');
    }
}
