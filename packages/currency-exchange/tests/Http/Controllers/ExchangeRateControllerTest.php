<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Tests\Http\Controllers;

use Petshop\CurrencyExchange\Facades\CurrencyExchange;
use Petshop\CurrencyExchange\Tests\Data\TestExchangeRate;
use Petshop\CurrencyExchange\Tests\TestCase;

class ExchangeRateControllerTest extends TestCase
{
    public function testItSendsRequestAndReturnsExchangeRateDetails()
    {
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

        $response = $this->getJson(route('currency-exchange.rates.convert', [
            'amount' => 320,
            'currency' => 'INR',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.source_currency', 'EUR')
            ->assertJsonPath('data.destination_currency', 'INR')
            ->assertJsonPath('data.source_amount', 320)
            ->assertJsonPath('data.exchange_rate', 89.59)
            ->assertJsonPath('data.converted_amount', 89.59 * 320);
    }
}
