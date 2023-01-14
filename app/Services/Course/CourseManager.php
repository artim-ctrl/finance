<?php

namespace App\Services\Course;

use GuzzleHttp\Exception\GuzzleException;

class CourseManager
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * @throws GuzzleException
     */
    public function getCourse(string $from, string $to, float $amount = 1): float
    {
        return $this->client->getCurrency($from, $to) * $amount;
    }
}
