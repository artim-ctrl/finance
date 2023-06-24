<?php

declare(strict_types = 1);

namespace App\Modules\Course;

use Illuminate\Support\Facades\Facade;

/**
 * @method static float getCourse(string $from, string $to)
 * @method static array getCourses(string $from, array $to)
 *
 * @see CourseManager
 */
final class Course extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CourseManager::class;
    }
}
