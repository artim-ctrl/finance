<?php

namespace App\Services\Course;

use GuzzleHttp\Client as ClientGuzzle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

class Client
{
    protected const BASE_URI = 'https://api.apilayer.com';
    protected const CACHE_PREFIX = 'currency';
    protected const CACHE_TTL = 24 * 60 * 60;

    /**
     * @throws GuzzleException
     */
    public function getCurrency(string $from, string $to): float
    {
        // TODO: crutch
        if ($from === $to) {
            return 1;
        }

        $cacheKey = $this->getCacheKey($from, $to);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->getClient()->get($this->getUri('/currency_data/live', [
            'source' => $from,
            'currencies' => $to,
        ]));

        $json = json_decode($response->getBody()->getContents(), true);

        Cache::put($cacheKey, $json['quotes'][$from.$to], static::CACHE_TTL);

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
     * @param array<int, string|array> $params
     * @return string
     */
    protected function getUri(string $uri, array $params): string
    {
        return $uri.'?'.http_build_query($params);
    }

    protected function getCacheKey(string $from, string $to): string
    {
        return static::CACHE_PREFIX.'_'.$from.$to;
    }
}
