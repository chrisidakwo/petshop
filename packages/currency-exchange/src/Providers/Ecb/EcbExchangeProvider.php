<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Providers\Ecb;

use Petshop\CurrencyExchange\Exception\ExchangeRateNotFound;
use Petshop\CurrencyExchange\ExchangeRate;
use Petshop\CurrencyExchange\Providers\AbstractExchangeProvider;
use SimpleXMLElement;

class EcbExchangeProvider extends AbstractExchangeProvider
{
    public const API_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    protected SimpleXMLElement $data;
    protected EcbApiClient $apiClient;

    public function __construct(EcbApiClient|null $apiClient = null)
    {
        $this->apiClient = $apiClient !== null ? $apiClient : new EcbApiClient();
    }

    public function getSourceCurrency(): string
    {
        return 'EUR';
    }

    /**
     * @return array<int, ExchangeRate>
     */
    public function getRates(): array
    {
        // TODO: Get from cache. If it does not exist, then get from API

        $this->data = $this->apiClient->getData(self::API_URL);

        $rates = [];

        foreach ($this->data->children() as $child) {
            $rates[] = new ExchangeRate(
                sourceCurrency: $this->getSourceCurrency(),
                destCurrency: (string) $child->attributes()->currency,
                exchangeAmount: (float) $child->attributes()->rate,
            );
        }

        if (count($rates) === 0) {
            throw new ExchangeRateNotFound('Could not retrieve exchange rates');
        }

        return $rates;
    }

    public function getRate(string $destCurrency): ExchangeRate
    {
        $this->data = $this->apiClient->getData(self::API_URL);

        foreach ($this->data->children() as $child) {
            if ((string) $child->attributes()->currency === $destCurrency) {
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
     * @return array<string>
     */
    public function getSupportedCurrencies(): array
    {
        return config('currency-exchange.supported_currencies');
    }
}
