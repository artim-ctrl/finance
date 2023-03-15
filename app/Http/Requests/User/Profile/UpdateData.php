<?php

namespace App\Http\Requests\User\Profile;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class UpdateData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
    ) {
    }
}
