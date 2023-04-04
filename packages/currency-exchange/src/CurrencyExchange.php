<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange;

use Illuminate\Contracts\Container\BindingResolutionException;
use PetShop\CurrencyExchange\Contracts\Cache;
use Petshop\CurrencyExchange\Contracts\CurrencyExchange as ICurrencyExchange;
use Petshop\CurrencyExchange\Contracts\ExchangeProvider;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;

class CurrencyExchange implements ICurrencyExchange
{
    protected string|null $sourceCurrency;
    protected string $destCurrency;
    protected Cache $cache;

    /**
     * @var array<string, string>
     */
    protected array $providers;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(Cache $cache)
    {
        $this->sourceCurrency = $this->getDefaultCurrency();
        $this->providers = app()->make('config')->get('currency-exchange.exchange_providers');
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(int|float $amount): ExchangeRate
    {
        return $this->getExchangeRate($this->destCurrency, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function from(string $currency = null): static
    {
        $this->sourceCurrency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
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
     * @throws InvalidCurrency
     */
    public function getExchangeRate(string $destCurrency, int|float $amount): ExchangeRate
    {
        $this->assertSourceAndDestinationCurrenciesAreValid();

        /** @var string $sourceCurrency */
        $sourceCurrency = $this->sourceCurrency;

        /** @var ExchangeProvider $provider */
        $provider = $this->providers[$sourceCurrency];

        $exchangeRate = $provider->convert($sourceCurrency, $destCurrency);

        return $exchangeRate->setSourceAmount($amount);
    }

    /**
     * Get default currency from cache
     */
    public function getDefaultCurrency(): ?string
    {
        return $this->cache->getItem('default_currency');
    }

    /**
     * @param non-empty-string|null $currency
     *
     * @throws BindingResolutionException
     */
    public function setDefaultCurrency(string $currency = null): static
    {
        $this->cache->saveItem(
            'default_currency',
            $currency !== null
                ? $currency
                : app()->make('config')->get('currency-exchange.default_currency')
        );

        return $this;
    }

    /**
     * @throws InvalidCurrency
     */
    protected function assertSourceAndDestinationCurrenciesAreValid(): void
    {
        /** @var array<int, string> $availableProviders */
        $availableProviders =  array_keys($this->providers);
        $sourceCurrency = $this->sourceCurrency;
        $destCurrency = $this->destCurrency;

        if (! in_array($sourceCurrency, $availableProviders)) {
            throw new InvalidCurrency("The source currency [$sourceCurrency] is not supported");
        }

        /** @var ExchangeProvider $provider */
        $provider = new $availableProviders[$this->sourceCurrency];

        if ($sourceCurrency !== $provider->getSourceCurrency()) {
            $providerClass = get_class($provider);
            throw new InvalidCurrency(
                "The provided source currency [$sourceCurrency] is not supported by [$providerClass]"
            );
        }

        if (! in_array($destCurrency, $provider->getSupportedCurrencies())) {
            throw new InvalidCurrency(
                "The provided destination currency [$destCurrency] is not supported"
            );
        }
    }
}
