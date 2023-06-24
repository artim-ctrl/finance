<?php

declare(strict_types = 1);

namespace App\Services\Currency;

use App\Models\Currency;
use App\Modules\Course\Course;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final readonly class GettingCourseService
{
    private const CACHE_PREFIX = 'currency';
    private const CACHE_TTL = 24 * 60 * 60;

    public function loadCoursesToCache(): void
    {
        $currencies = Currency::select('code')->pluck('code');
        $currencies->each(function (string $currency) use ($currencies) {
            $courses = Course::getCourses($currency, $currencies->all());
            /** @var float $course */
            foreach ($courses as $key => $course) {
                $this->set($key, $this->prepareCourse($course));
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

        $course = $this->prepareCourse(Course::getCourse($from, $to));

        $this->set($from.$to, $course);

        return $course * $amount;
    }

    public function flush(): void
    {
        $this->cache()->flush();
    }

    protected function set(string $key, float $course): void
    {
        $this->cache()->put($this->getCacheKey($key), $course, self::CACHE_TTL);
    }

    protected function has(string $key): bool
    {
        return $this->cache()->has($this->getCacheKey($key));
    }

    protected function get(string $key): float
    {
        return $this->cache()->get($this->getCacheKey($key));
    }

    protected function cache(): TaggedCache
    {
        return Cache::tags(['courses']);
    }

    protected function prepareCourse(float $course): float
    {
        return round($course, 5);
    }

    protected function getCacheKey(string $fromTo): string
    {
        return self::CACHE_PREFIX.'_'.$fromTo;
    }
}
