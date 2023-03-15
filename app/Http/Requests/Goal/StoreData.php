<?php

namespace App\Http\Requests\Goal;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class StoreData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
    ) {
    }
}