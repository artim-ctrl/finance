<?php

namespace App\Modules\Course;

use GuzzleHttp\Client as ClientGuzzle;
use GuzzleHttp\Exception\GuzzleException;

class CourseManager
{
    protected const BASE_URI = 'https://api.apilayer.com';

    /**
     * @param array<int, string> $currencies
     *
     * @return array<string, float>
     *
     * @throws GuzzleException
     */
    public function getCourses(string $sourceCurrency, array $currencies): array
    {
        $response = $this->getClient()->get($this->getUri('/currency_data/live', [
            'source' => $sourceCurrency,
            'currencies' => implode(',', $currencies),
        ]));

        $json = json_decode($response->getBody()->getContents(), true);

        return $json['quotes'];
    }

    /**
     * @throws GuzzleException
     */
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

        $json = json_decode($response->getBody()->getContents(), true);

        return $json['quotes'][$from.$to];
    }

    protected function getClient(): ClientGuzzle
    {
        return new ClientGuzzle([
            'base_uri' => static::BASE_URI,
            'headers' => ['apiKey' => config('apilayer.key')],
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
