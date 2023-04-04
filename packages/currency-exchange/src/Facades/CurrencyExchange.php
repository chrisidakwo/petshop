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
 * @method static \Petshop\CurrencyExchange\CurrencyExchange setSourceCurrency(string $currency)
 * @method static string getSourceCurrency()
 *
 * @see \Petshop\CurrencyExchange\CurrencyExchange
 */
class CurrencyExchange extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Petshop\CurrencyExchange\Contracts\CurrencyExchange::class;
    }
}
