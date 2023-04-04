<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Contracts;

use Petshop\CurrencyExchange\Exception\ExchangeRateNotFound;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;
use Petshop\CurrencyExchange\ExchangeRate;

interface ExchangeProvider
{
    /**
     * Get the exchange rates for the day
     *
     * @return array<int, ExchangeRate>
     *
     * @throws ExchangeRateNotFound
     */
    public function getRates(): array;

    /**
     * If exchange rate is found, it returns an ExchangeRate object. Else, it throws an ExchangeRateNotFound exception
     *
     * Throws an InvalidCurrency exception if either of source or destination currency is not found
     *
     * @throws InvalidCurrency|ExchangeRateNotFound
     */
    public function getRate(string $destCurrency): ExchangeRate;

    /**
     * Get the source currency for the provider
     */
    public function getSourceCurrency(): string;

    /**
     * Returns a list of currencies the exchange provider supports
     *
     * @return array<string>
     */
    public function getSupportedCurrencies(): array;
}
