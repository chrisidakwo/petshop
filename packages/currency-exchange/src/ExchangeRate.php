<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange;

class ExchangeRate
{
    protected string $sourceCurrency;
    protected int|float $sourceAmount;
    protected string|null $destCurrency;
    protected int|float $exchangeAmount;

    public function __construct(
        string $sourceCurrency,
        string $destCurrency = null,
        int|float $sourceAmount = 0,
        int|float $exchangeAmount = 0,
    )
    {
        $this->sourceCurrency = $sourceCurrency;
        $this->sourceAmount = $sourceAmount;
        $this->destCurrency = $destCurrency;
        $this->exchangeAmount = $exchangeAmount;
    }

    public function setSourceCurrency(string $sourceCurrency): ExchangeRate
    {
        $this->sourceCurrency = $sourceCurrency;

        return $this;
    }

    public function setSourceAmount(float|int $sourceAmount): ExchangeRate
    {
        $this->sourceAmount = $sourceAmount;

        return $this;
    }

    public function setDestCurrency(string $destCurrency): ExchangeRate
    {
        $this->destCurrency = $destCurrency;

        return $this;
    }

    public function setExchangeAmount(float|int $exchangeAmount): ExchangeRate
    {
        $this->exchangeAmount = $exchangeAmount;

        return $this;
    }

    public function getSourceCurrency(): string
    {
        return $this->sourceCurrency;
    }

    public function getSourceAmount(): float|int
    {
        return $this->sourceAmount;
    }

    public function getDestCurrency(): ?string
    {
        return $this->destCurrency;
    }

    public function getExchangeAmount(): float|int
    {
        return $this->exchangeAmount;
    }

    public function getConvertedAmount(): int|float
    {
        return $this->exchangeAmount * $this->sourceAmount;
    }
}
