<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Contracts;

use Petshop\CurrencyExchange\Exception\ExchangeRateNotFound;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;
use Petshop\CurrencyExchange\ExchangeRate;

interface ExchangeProvider
{
    /**
     * Get exchange rates for the provided date.
     *
     * The $fallbackPeriod allows the designation of a period of time for which the API can search backwards
     * to get the last updated exchange rate. Certain APIs might not update exchange rates over the weekend,
     * or if the rate does not change. Hence, this allows for returning a valid value without having
     * the exception thrown
     *
     * @return array<string, float>
     *
     * @throws ExchangeRateNotFound
     */
    public function getRates(string $date, int $fallBackPeriod = 7): array;

    /**
     * If exchange rate is found, it returns an ExchangeRate object. Else, it throws an ExchangeRateNotFound exception
     *
     * Throws an InvalidCurrency exception if either of source or destination currency is not found
     *
     * @throws InvalidCurrency|ExchangeRateNotFound
     */
    public function convert(string $destCurrency, string $date = null): ExchangeRate;

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
