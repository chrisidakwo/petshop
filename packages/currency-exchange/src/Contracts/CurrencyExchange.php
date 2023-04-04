<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Contracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;
use Petshop\CurrencyExchange\ExchangeRate;

interface CurrencyExchange
{
    /**
     * @throws BindingResolutionException|InvalidCurrency
     */
    public function convert(int|float $amount): ExchangeRate;

    /**
     * Destination currency
     *
     * @param non-empty-string $currency
     */
    public function to(string $currency): static;

    /**
     * Source currency
     */
    public function from(string $currency = null): static;
}
