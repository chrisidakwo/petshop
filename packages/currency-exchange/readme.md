## Currency Exchange

A package for easily converting currencies based on current exchange rates provided by the [European Central Bank](https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml)


### How To Use

1. To convert from one currency to another using the fluent methods:
```php
<?php

use Petshop\CurrencyExchange\CurrencyExchange;

$providers = config('currency-exchange.exchange_providers');

$currencyExchange = new CurrencyExchange(
    $providers,
);

$exchangeRate = $currencyExchange->from('EUR')->to('INR')->convert(320);

// Returns 320
$exchangeRate->getSourceAmount();

// Returns INR
$exchangeRate->getDestCurrency();

// Returns the exchange rate amount
$exchangeRate->getExchangeAmount();

// Gets the converted amount from EUR to INR
$exchangeRate->getConvertedAmount();
```

2. To convert from one currency to another using the `getExchangeRate()` methods:
```php
<?php

use Petshop\CurrencyExchange\CurrencyExchange;

$providers = config('currency-exchange.exchange_providers');

$currencyExchange = new CurrencyExchange(
    $providers,
);

$exchangeRate = $currencyExchange->getExchangeRate('INR', 320);
```

3. To convert from one currency to another using the facade:
```php
<?php

use Petshop\CurrencyExchange\Facades;

$exchangeRate =  CurrencyExchange::to('USD')->convert(918);
$exchangeRate = CurrencyExchange::getExchangeRate('INR', 320);
```
With the facade, the currency converter object is bootstrapped with the defaults available in the config.

4. The package exposes a GET route: `api/v1/currency-exchange/convert`. This can be modified in the available config. The route requires an amount and a destination currency.
```http request
GET http://localhost:9900/api/v1/currency-exchange/convert
Content-Type: application/json
Accept: application/json

{
  "amount": 380,
  "currency": "NOK",
}
```

In the available config, you can define middlewares for the route using the `route.configurations` key.

#### Note

You do not need to provide the `->from()` method when interacting with a `CurrencyExchange` object (as is shown in the first example). The source currency can be easily retrieved from the config.



