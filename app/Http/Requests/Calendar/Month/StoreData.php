<?php

declare(strict_types = 1);

namespace App\Http\Requests\Calendar\Month;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[In('first', 'left', 'right')]
        public string $to,
    ) {
    }
}
