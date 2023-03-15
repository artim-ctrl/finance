<?php

namespace App\Http\Requests\Currency\Course;

use Spatie\LaravelData\Data;

class IndexData extends Data
{
    /**
     * @param array<int, string> $currencies
     */
    public function __construct(
        public array $currencies,
    ) {
    }
}
