<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Providers\Ecb;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Petshop\CurrencyExchange\Exception\ExchangeRateNotFound;
use Petshop\CurrencyExchange\ExchangeRate;
use Petshop\CurrencyExchange\Providers\AbstractExchangeProvider;
use SimpleXMLElement;

class EcbExchangeProvider extends AbstractExchangeProvider
{
    public const API_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    protected SimpleXMLElement $data;
    protected EcbApiClient $apiClient;

    public function __construct(Application $app, EcbApiClient $apiClient = null)
    {
        parent::__construct($app);

        $this->apiClient = $apiClient !== null ? $apiClient : new EcbApiClient();
    }

    /**
     * @inheritDoc
     */
    public function getSourceCurrency(): string
    {
        return 'EUR';
    }

    /**
     * {@inheritdoc}
     */
    public function getRates(): array
    {
        $this->data = $this->apiClient->getData(self::API_URL);

        $rates = [];

        foreach ($this->data->children() as $child) {
            $rates[] = new ExchangeRate(
                sourceCurrency: $this->getSourceCurrency(),
                destCurrency: (string) $child->attributes()->currency,
                exchangeAmount: (float) $child->attributes()->rate,
            );
        }

        return $rates;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate(string $destCurrency): ExchangeRate
    {
        $this->data = $this->apiClient->getData(self::API_URL);

        foreach ($this->data->children() as $child) {
            if ((string) $child->attributes()->currency == $destCurrency) {
                return new ExchangeRate(
                    sourceCurrency: $this->getSourceCurrency(),
                    destCurrency: $destCurrency,
                    exchangeAmount: (float) $child->attributes()->rate,
                );
            }
        }

        throw new ExchangeRateNotFound(
            'Could not get exchange rate for the provided destination currency'
        );
    }

    /**
     * @inheritDoc
     *
     * @throws BindingResolutionException
     */
    public function getSupportedCurrencies(): array
    {
        return $this->app->make('config')->get('currency-exchange.supported_currencies');
    }
}
