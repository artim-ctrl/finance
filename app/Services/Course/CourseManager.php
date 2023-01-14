<?php

namespace App\Services\Course;

class CourseManager
{
    public function __construct(protected Client $client)
    {
    }

    public function getCourse(string $from, string $to, float $amount = 1): float
    {
        return $this->client->getCurrency($from, $to) * $amount;
    }
}
