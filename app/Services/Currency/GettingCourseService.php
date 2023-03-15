<?php

namespace App\Services\Currency;

use App\Models\Currency;
use App\Modules\Course\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GettingCourseService
{
    protected const CACHE_PREFIX = 'currency';
    protected const CACHE_TTL = 24 * 60 * 60;

    public function loadCoursesToCache(): void
    {
        /** @var Collection<int, string> $currencies */
        $currencies = Currency::query()->select('code')->get('code');
        $currencies->each(function (string $currency) use ($currencies) {
            $courses = Course::getCourses($currency, $currencies->all());
            /** @var float $course */
            foreach ($courses as $key => $course) {
                $this->set($key, $course);
            }
        });
    }

    public function calcAmount(string $from, string $to, float $amount = 1.0): float
    {
        if ($from === $to) {
            return $amount;
        }

        if ($this->has($from.$to)) {
            return $this->get($from.$to) * $amount;
        }

        $course = Course::getCourse($from, $to);

        $this->set($from.$to, $course);

        return $course * $amount;
    }

    protected function set(string $key, float $course): void
    {
        Cache::put($this->getCacheKey($key), $course, static::CACHE_TTL);
    }

    protected function has(string $key): bool
    {
        return Cache::has($this->getCacheKey($key));
    }

    protected function get(string $key): float
    {
        return Cache::get($this->getCacheKey($key));
    }

    protected function getCacheKey(string $fromTo): string
    {
        return static::CACHE_PREFIX.'_'.$fromTo;
    }
}
