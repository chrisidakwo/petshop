<?php

use Illuminate\Support\Facades\Facade;
use Petshop\CurrencyExchange\ExchangeRate;

/**
 * @method static ExchangeRate convert(string $sourceCurrency, string $destCurrency, int|float $amount)
 * @method static ExchangeRate getRate(string $destCurrency, int|float $amount)
 */
class CurrencyExchange extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'currency-exchange';
    }
}
