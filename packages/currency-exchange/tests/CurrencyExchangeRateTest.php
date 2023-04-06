<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery;
use Mockery\MockInterface;
use Petshop\CurrencyExchange\Contracts\CurrencyExchange;
use Petshop\CurrencyExchange\Contracts\ExchangeProvider;
use Petshop\CurrencyExchange\Exception\InvalidCurrency;
use Petshop\CurrencyExchange\ExchangeRate;
use Petshop\CurrencyExchange\Providers\Ecb\EcbExchangeProvider;
use Petshop\CurrencyExchange\Tests\Data\TestExchangeRate;

class CurrencyExchangeRateTest extends TestCase
{
    protected CurrencyExchange $currencyExchange;
    protected ExchangeProvider|MockInterface $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = Mockery::mock(EcbExchangeProvider::class);

        $this->currencyExchange = new \Petshop\CurrencyExchange\CurrencyExchange(
            providers: [
                'EUR' => $this->provider,
            ]
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function testItReturnsTheDefaultCurrencyWhenNoSourceCurrencyIsSet(): void
    {
        $currency = $this->currencyExchange->getSourceCurrency();

        $this->assertSame('EUR', $currency);
    }

    /**
     * @throws BindingResolutionException
     */
    public function testItThrowsAnExceptionForAnUnSupportedDestinationCurrency(): void
    {
        $this->expectException(InvalidCurrency::class);
        $this->expectExceptionMessage('The provided destination currency [NGN] is not supported');

        $this->provider->shouldReceive('getSourceCurrency')->once()->andReturn('EUR');
        $this->provider->shouldReceive('getSupportedCurrencies')->once()->andReturn(
            config('currency-exchange.supported_currencies')
        );

        $this->currencyExchange->getExchangeRate('NGN', 23);
    }

    public function testItGetsExchangeRateForAValidDestinationCountry(): void
    {
        $this->provider->shouldReceive('getSourceCurrency')->once()->andReturn('EUR');
        $this->provider->shouldReceive('getSupportedCurrencies')->once()->andReturn(
            config('currency-exchange.supported_currencies')
        );

        $this->provider->shouldReceive('getRate')->once()
            ->andReturn(new TestExchangeRate(
                'EUR',
                'USD',
                23,
                23 * 1.0870
            ));

        $result = $this->currencyExchange->getExchangeRate('USD', 23);

        $this->assertSame('EUR', $result->getSourceCurrency());
        $this->assertSame(23, $result->getSourceAmount());
        $this->assertSame(23 *  $result->getExchangeAmount(), $result->getConvertedAmount());
        $this->assertInstanceOf(ExchangeRate::class, $result);
    }

    public function testItGetsExchangeRateWithFluentMethods(): void
    {
        $this->provider->shouldReceive('getSourceCurrency')->once()->andReturn('EUR');
        $this->provider->shouldReceive('getSupportedCurrencies')->once()->andReturn(
            config('currency-exchange.supported_currencies')
        );

        $this->provider->shouldReceive('getRate')->once()
            ->andReturn(new TestExchangeRate(
                'EUR',
                'INR',
                180,
                89.4710
            ));

        $result = $this->currencyExchange->to('INR')->convert(180);

        $this->assertSame('EUR', $result->getSourceCurrency());
        $this->assertSame('INR', $result->getDestCurrency());
        $this->assertSame(180, $result->getSourceAmount());
        $this->assertSame(180 *  $result->getExchangeAmount(), $result->getConvertedAmount());
        $this->assertInstanceOf(ExchangeRate::class, $result);
    }
}
