<?php

use Illuminate\Support\Facades\Route;
use Petshop\CurrencyExchange\Http\Controllers\ExchangeRateController;

Route::group(config('currency-exchange.route.configurations'), function (): void {
    Route::get('convert', [ExchangeRateController::class, 'convert'])
        ->name(config('currency-exchange.route.name'));
});
