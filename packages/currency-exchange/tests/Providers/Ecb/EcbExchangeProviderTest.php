<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Tests\Providers\Ecb;

use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery;
use Mockery\MockInterface;
use Petshop\CurrencyExchange\Contracts\ExchangeProvider;
use Petshop\CurrencyExchange\ExchangeRate;
use Petshop\CurrencyExchange\Providers\Ecb\EcbApiClient;
use Petshop\CurrencyExchange\Providers\Ecb\EcbExchangeProvider;
use Petshop\CurrencyExchange\Tests\TestCase;
use SimpleXMLElement;

class EcbExchangeProviderTest extends TestCase
{
    protected ExchangeProvider $provider;
    protected EcbApiClient|MockInterface $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiClient = Mockery::mock(EcbApiClient::class);

        $this->provider = new EcbExchangeProvider(
            app: $this->app,
            apiClient: $this->apiClient,
        );
    }

    public function testItGetsTheExchangeRateForAValidDestinationCurrency(): void
    {
        $this->apiClient->shouldReceive('getData')
            ->once()
            ->andReturn($this->getTestSimpleXml());

        $exchangeRate = $this->provider->getRate('INR');

        $this->assertInstanceOf(ExchangeRate::class, $exchangeRate);
        $this->assertEquals('INR', $exchangeRate->getDestCurrency());
        $this->assertEquals('EUR', $exchangeRate->getSourceCurrency());
    }

    public function testItGetsAllRatesForTheDay(): void
    {
        $this->apiClient->shouldReceive('getData')
            ->once()
            ->andReturn($this->getTestSimpleXml());

        $exchangeRates = $this->provider->getRates();

        $this->assertCount(30, $exchangeRates);
        $this->assertEquals('EUR', $exchangeRates[0]->getSourceCurrency());
        $this->assertNotEmpty($exchangeRates[0]->getDestCurrency());
    }

    /**
     * @throws BindingResolutionException
     */
    public function testItGetsSupportedCurrencies()
    {
        $result = $this->provider->getSupportedCurrencies();

        $this->assertIsArray($result);
        $this->assertContains('EUR', $result);
    }

    protected function getTestSimpleXml(): SimpleXMLElement
    {
        $data = file_get_contents(__DIR__ . '/../../Data/response.xml');

        return (new SimpleXMLElement($data))->Cube[0]->Cube[0];
    }
}
