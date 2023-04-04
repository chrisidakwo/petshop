<?php

namespace Petshop\CurrencyExchange;

use Petshop\CurrencyExchange\Exception\InvalidCurrencyException;

class CurrencyExchange
{
    /**
     * Retrieves the current exchange rate of currently active currency to a provided destination currency.
     * Returns an ExchangeRate object if destination currency is valid.
     * Else, throws InvalidCurrencyException if the provided destination currency is not supported/available.
     *
     * @throws InvalidCurrencyException
     */
    public function getRate(string $destCurrency, int|float $amount): ExchangeRate
    {

    }

    /**
     * This is a conversion between two currencies.
     * Returns an ExchangeRate object if destination currency is valid.
     * Else, throws InvalidCurrencyException if any of the provided currencies are not supported/available.
     *
     * @throws InvalidCurrencyException
     */
    public function convert(string $sourceCurrency, string $destCurrency, int|float $amount): ExchangeRate
    {

    }
}
