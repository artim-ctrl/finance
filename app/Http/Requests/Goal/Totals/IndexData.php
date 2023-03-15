<?php

namespace App\Http\Requests\Goal\Totals;

use Spatie\LaravelData\Data;

class IndexData extends Data
{
    /**
     * @param array<string, array<string, float>> $courses
     */
    public function __construct(
        public array $courses,
    ) {
    }
}
