<?php

namespace App\Http\Requests\Calendar\Month;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

class StoreData extends Data
{
    public function __construct(
        #[In('first', 'left', 'right')]
        public string $to,
    ) {
    }
}
