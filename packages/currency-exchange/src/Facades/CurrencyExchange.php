<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Facades;

use Illuminate\Support\Facades\Facade;
use Petshop\CurrencyExchange\ExchangeRate;

/**
 * @method static ExchangeRate convert(int|float $amount)
 * @method static \Petshop\CurrencyExchange\Contracts\CurrencyExchange from(string|null $sourceCurrency)
 * @method static \Petshop\CurrencyExchange\Contracts\CurrencyExchange to(string $destCurrency)
 * @method static ExchangeRate getExchangeRate(string $destCurrency, int|float $amount)
 * @method static \Petshop\CurrencyExchange\CurrencyExchange setDefaultCurrency(string $currency = null)
 * @method static string|null getDefaultCurrency()
 *
 * @see \Petshop\CurrencyExchange\CurrencyExchange
 */
class CurrencyExchange extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'currency-exchange';
    }
}
