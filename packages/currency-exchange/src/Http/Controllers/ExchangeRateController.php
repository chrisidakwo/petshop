<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Petshop\CurrencyExchange\Facades\CurrencyExchange;

class ExchangeRateController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function convert(Request $request): JsonResponse
    {
        $supportedCurrencies = config('currency-exchange.supported_currencies');
        $defaultCurrency = config('currency-exchange.default_currency');
        $requestCurrency = $request->get('currency');

        $currencies = array_filter($supportedCurrencies, fn ($value) => $value !== $defaultCurrency);

        $validated = $request->validate([
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string', Rule::in($currencies)],
        ],  [
            'currency.in' => "We do not support converting from {$defaultCurrency} to {$requestCurrency}",
        ]);

        $exchangeRate = CurrencyExchange::to($validated['currency'])
            ->convert((float) $validated['amount']);

        return response()->json([
            'success' => 1,
            'data' => [
                'source_currency' => $exchangeRate->getSourceCurrency(),
                'destination_currency' => $exchangeRate->getDestCurrency(),
                'source_amount' => $exchangeRate->getSourceAmount(),
                'exchange_rate' => $exchangeRate->getExchangeAmount(),
                'converted_amount' => $exchangeRate->getConvertedAmount(),
            ],
            'error' => null,
            'errors' => [],
            'extra' => [],
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
