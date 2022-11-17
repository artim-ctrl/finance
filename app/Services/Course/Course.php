<?php

namespace App\Services\Course;

use Illuminate\Support\Facades\Facade;

/**
 * @method static float rubToTry(float $amount)
 * @method static float rubToUsd(float $amount)
 * @method static float usdToTry(float $amount)
 * @method static float usdToRub(float $amount)
 * @method static float tryToUsd(float $amount)
 * @method static float tryToRub(float $amount)
 * @method static float getCourse(string $from, string $to, float $amount = 1)
 *
 * @see CourseManager
 */
class Course extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CourseManager::class;
    }
}
