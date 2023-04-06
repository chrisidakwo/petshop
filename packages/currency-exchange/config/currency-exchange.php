<?php

return [
    'default_currency' => 'EUR',
    'exchange_providers' => [
        'EUR' => Petshop\CurrencyExchange\Providers\Ecb\EcbExchangeProvider::class,
    ],
    'route' => [
        'name' => 'convert',
        'configurations' => [
            'prefix' => 'api/v1/currency-exchange',
            'as' => 'api.currency-exchange.',
        ],
    ],
    'supported_currencies' => [
        'EUR',
        'USD',
        'JPY',
        'BGN',
        'CZK',
        'DKK',
        'GBP',
        'HUF',
        'PLN',
        'RON',
        'SEK',
        'CHF',
        'ISK',
        'NOK',
        'TRY',
        'AUD',
        'BRL',
        'CAD',
        'CNY',
        'HKD',
        'IDR',
        'ILS',
        'INR',
        'KRW',
        'MXN',
        'MYR',
        'NZD',
        'PHP',
        'SGD',
        'THB',
        'ZAR',
    ]
];
