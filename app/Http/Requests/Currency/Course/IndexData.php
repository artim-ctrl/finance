<?php

declare(strict_types = 1);

namespace App\Http\Requests\Currency\Course;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class IndexData extends Data
{
    /**
     * @param array<int, string> $currencies
     */
    public function __construct(
        #[Required]
        public array $currencies,
    ) {
    }
}
