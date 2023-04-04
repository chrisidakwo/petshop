<?php

use Petshop\CurrencyExchange\Http\Controllers\ExchangeRateController;

Route::get(
    '/currency-exchange/convert',
    [ExchangeRateController::class, 'convert']
)->name('currency-exchange.rates.convert');
