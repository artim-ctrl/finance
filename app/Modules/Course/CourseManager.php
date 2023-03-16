<?php

namespace App\Modules\Course;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CourseManager
{
    protected const BASE_URI = 'https://api.apilayer.com';

    /**
     * @param array<int, string> $currencies
     *
     * @return array<string, float>
     */
    public function getCourses(string $sourceCurrency, array $currencies): array
    {
        $response = $this->getClient()->get($this->getUri('/currency_data/live', [
            'source' => $sourceCurrency,
            'currencies' => implode(',', $currencies),
        ]));

        return $response->json('quotes');
    }

    public function getCourse(string $from, string $to): float
    {
        // TODO: crutch
        if ($from === $to) {
            return 1;
        }

        $response = $this->getClient()->get($this->getUri('/currency_data/live', [
            'source' => $from,
            'currencies' => $to,
        ]));

        return $response->json('quotes.'.$from.$to);
    }

    protected function getClient(): PendingRequest
    {
        return Http::baseUrl(static::BASE_URI)->withHeaders([
            'apiKey' => config('apilayer.key'),
        ]);
    }

    /**
     * @param string $uri
     * @param array<string, string> $params
     * @return string
     */
    protected function getUri(string $uri, array $params): string
    {
        return $uri.'?'.http_build_query($params);
    }
}
