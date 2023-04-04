<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Petshop\CurrencyExchange\Contracts\CurrencyExchange as ICurrencyExchange;
use Petshop\CurrencyExchange\Contracts\ExchangeProvider;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;

class CurrencyExchange implements ICurrencyExchange
{
    protected string|null $sourceCurrency;
    protected string $destCurrency;
    protected Application $app;
    protected ExchangeProvider|null $provider;

    /**
     * @var array<string, string>
     */
    protected array $providers;

    /**
     * @param array<string, string> $providers
     */
    public function __construct(Application $app, array $providers)
    {
        $this->sourceCurrency = null;

        $this->providers = $providers;
        $this->app = $app;
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
     * @throws InvalidCurrency|BindingResolutionException
     */
    public function getExchangeRate(string $destCurrency, int|float $amount): ExchangeRate
    {
        $this->destCurrency = $destCurrency;
        $this->sourceCurrency = $this->getSourceCurrency();

        $this->assertSourceAndDestinationCurrenciesAreValid();

        $exchangeRate = $this->provider->getRate($this->destCurrency);

        return $exchangeRate->setSourceAmount($amount);
    }

    /**
     * Get default currency from cache
     *
     * @throws BindingResolutionException
     */
    public function getSourceCurrency(): string
    {
        /** @var string $defaultCurrency */
        $defaultCurrency = $this->app->make('config')->get('currency-exchange.default_currency');

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
        $availableProviders = $this->providers;

        /** @var array<int, string> $availableProviders */
        $providersKeys = array_keys($availableProviders);

        $sourceCurrency = $this->sourceCurrency;
        $destCurrency = $this->destCurrency;

        if (! in_array($sourceCurrency, $providersKeys)) {
            throw new InvalidCurrency("The source currency [$sourceCurrency] is not supported");
        }

        /** @var ExchangeProvider $provider */
        $this->provider = $provider = match(is_string($availableProviders[$sourceCurrency])) {
            true => new $availableProviders[$sourceCurrency](
                $this->app,
            ),
            false => $availableProviders[$sourceCurrency],
        };

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
