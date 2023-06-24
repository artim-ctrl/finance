<?php

declare(strict_types = 1);

namespace App\Http\Requests\Balance;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpdateData extends Data
{
    public function __construct(
        #[Required]
        public float $amount,
    ) {
    }
}
