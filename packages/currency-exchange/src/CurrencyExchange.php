<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange;

use Illuminate\Contracts\Container\BindingResolutionException;
use Petshop\CurrencyExchange\Contracts\CurrencyExchange as ICurrencyExchange;
use Petshop\CurrencyExchange\Contracts\ExchangeProvider;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;

class CurrencyExchange implements ICurrencyExchange
{
    protected string|null $sourceCurrency;
    protected string|null $destCurrency;
    protected ExchangeProvider|null $provider;

    /**
     * @var array<string, string>
     */
    protected array $providers;

    /**
     * @param array<string, string> $providers
     */
    public function __construct(array $providers)
    {
        $this->sourceCurrency = null;
        $this->destCurrency = null;

        $this->providers = $providers;
    }

    public function convert(int|float $amount): ExchangeRate
    {
        return $this->getExchangeRate($this->destCurrency, $amount);
    }

    public function from(string|null $currency = null): static
    {
        $this->sourceCurrency = $currency;

        return $this;
    }

    public function to(string $currency): static
    {
        $this->destCurrency = $currency;

        return $this;
    }

    /**
     * Retrieves the current exchange rate of currently active currency to a provided destination currency.
     * Returns an ExchangeRate object if destination currency is valid.
     * Else, throws InvalidCurrencyException if the provided destination currency is not supported/available.
     *
     * @throws InvalidCurrency|BindingResolutionException
     */
    public function getExchangeRate(string $destCurrency, int|float $amount): ExchangeRate
    {
        $this->destCurrency = $destCurrency;
        $this->sourceCurrency = $this->getSourceCurrency();

        $this->assertSourceAndDestinationCurrenciesAreValid();

        $exchangeRate = $this->provider->getRate($destCurrency);

        return $exchangeRate->setSourceAmount($amount);
    }

    /**
     * Get default currency from cache
     */
    public function getSourceCurrency(): string
    {
        /** @var string $defaultCurrency */
        $defaultCurrency = config('currency-exchange.default_currency');

        return $this->sourceCurrency !== null ? $this->sourceCurrency : $defaultCurrency;
    }

    /**
     * @param non-empty-string $currency
     */
    public function setSourceCurrency(string $currency): static
    {
        $this->sourceCurrency = $currency;

        return $this;
    }

    /**
     * @throws InvalidCurrency
     */
    protected function assertSourceAndDestinationCurrenciesAreValid(): void
    {
        $sourceCurrency = $this->sourceCurrency;
        $destCurrency = $this->destCurrency;

        if (! in_array($sourceCurrency, array_keys($this->providers))) {
            throw new InvalidCurrency("The source currency [{$sourceCurrency}] is not supported");
        }

        $this->provider = $provider = match (is_string($this->providers[$sourceCurrency])) {
            true => new $this->providers[$sourceCurrency](),
            false => $this->providers[$sourceCurrency],
        };

        /** @var ExchangeProvider $provider */

        if ($sourceCurrency !== $provider->getSourceCurrency()) {
            $providerClass = $provider::class;
            throw new InvalidCurrency(
                "The provided source currency [{$sourceCurrency}] is not supported by [{$providerClass}]"
            );
        }

        if ($destCurrency === null || ! in_array($destCurrency, $provider->getSupportedCurrencies())) {
            throw new InvalidCurrency(
                "The provided destination currency [{$destCurrency}] is not supported"
            );
        }
    }
}
