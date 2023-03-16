<?php

namespace App\Http\Requests\Goal\Totals;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class IndexData extends Data
{
    /**
     * @param array<string, array<string, float>>|Optional $courses
     */
    public function __construct(
        public array|Optional $courses,
    ) {
    }
}
