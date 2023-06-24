<?php

declare(strict_types = 1);

namespace App\Http\Requests\Goal;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Max(255)]
        public string $name,
    ) {
    }
}
