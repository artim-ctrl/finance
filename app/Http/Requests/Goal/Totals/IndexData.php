<?php

namespace App\Http\Requests\Goal\Totals;

use Spatie\LaravelData\Data;

class IndexData extends Data
{
    public function __construct(
        public array $courses,
    ) {
    }
}
