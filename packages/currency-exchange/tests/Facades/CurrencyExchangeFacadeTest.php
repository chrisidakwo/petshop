<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Tests\Facades;

use Petshop\CurrencyExchange\ExchangeRate;
use Petshop\CurrencyExchange\Facades\CurrencyExchange;
use Petshop\CurrencyExchange\Tests\Data\TestExchangeRate;
use Petshop\CurrencyExchange\Tests\TestCase;

class CurrencyExchangeFacadeTest extends TestCase
{
    public function testItProperlyResolvesFacade()
    {
        CurrencyExchange::shouldReceive('from')->once()->andReturnSelf();
        CurrencyExchange::shouldReceive('to')->once()->andReturnSelf();
        CurrencyExchange::shouldReceive('convert')->once()
            ->andReturn(
                new TestExchangeRate(
                    sourceCurrency: 'EUR',
                    destCurrency: 'INR',
                    sourceAmount: 320,
                    exchangeAmount: 89.59,
                )
            );

        $result = CurrencyExchange::from('EUR')->to('INR')->convert(320);

        $this->assertInstanceOf(ExchangeRate::class, $result);
        $this->assertEquals('EUR', $result->getSourceCurrency());
        $this->assertEquals('INR', $result->getDestCurrency());
        $this->assertEquals(320, $result->getSourceAmount());
        $this->assertEquals(89.59, $result->getExchangeAmount());
        $this->assertEquals(320 * 89.59, $result->getConvertedAmount());
    }
}
