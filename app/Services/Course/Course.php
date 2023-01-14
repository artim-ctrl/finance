<?php

namespace App\Services\Course;

use Illuminate\Support\Facades\Facade;

/**
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
