<?php

namespace App\Services\Currency;

class CurrencyManager
{
    public function __construct(protected Client $client)
    {
    }

    public function usdToRub(float $amount): float
    {
        return $this->getCurrency('USD', 'RUB', $amount);
    }

    public function usdToTry(float $amount): float
    {
        return $this->getCurrency('USD', 'TRY', $amount);
    }

    public function rubToUsd(float $amount): float
    {
        return $this->getCurrency('RUB', 'USD', $amount);
    }

    public function rubToTry(float $amount): float
    {
        return $this->getCurrency('RUB', 'TRY', $amount);
    }

    public function tryToUsd(float $amount): float
    {
        return $this->getCurrency('TRY', 'USD', $amount);
    }

    public function tryToRub(float $amount): float
    {
        return $this->getCurrency('TRY', 'RUB', $amount);
    }

    protected function getCurrency(string $from, string $to, float $amount): float
    {
        return $this->client->getCurrency($from, $to) * $amount;
    }
}
