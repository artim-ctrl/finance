<?php

namespace App\Services\Course;

class CourseManager
{
    public function __construct(protected Client $client)
    {
    }

    public function usdToRub(float $amount): float
    {
        return $this->getCourse('USD', 'RUB', $amount);
    }

    public function usdToTry(float $amount): float
    {
        return $this->getCourse('USD', 'TRY', $amount);
    }

    public function rubToUsd(float $amount): float
    {
        return $this->getCourse('RUB', 'USD', $amount);
    }

    public function rubToTry(float $amount): float
    {
        return $this->getCourse('RUB', 'TRY', $amount);
    }

    public function tryToUsd(float $amount): float
    {
        return $this->getCourse('TRY', 'USD', $amount);
    }

    public function tryToRub(float $amount): float
    {
        return $this->getCourse('TRY', 'RUB', $amount);
    }

    public function getCourse(string $from, string $to, float $amount = 1): float
    {
        return $this->client->getCurrency($from, $to) * $amount;
    }
}
