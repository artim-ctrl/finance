<?php

namespace App\Modules\Course;

use Illuminate\Support\Facades\Facade;

/**
 * @method static float getCourse(string $from, string $to)
 * @method static array getCourses(string $from, array $to)
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
