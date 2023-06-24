<?php

declare(strict_types = 1);

namespace App\Http\Requests\Goal\Totals;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class IndexData extends Data
{
    /**
     * @param array<string, array<string, float>>|Optional $courses
     */
    public function __construct(
        #[Required]
        public array|Optional $courses,
    ) {
    }
}
