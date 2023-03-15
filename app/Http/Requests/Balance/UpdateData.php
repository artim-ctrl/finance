<?php

namespace App\Http\Requests\Balance;

use Spatie\LaravelData\Data;

class UpdateData extends Data
{
    public function __construct(
        public float $amount,
    ) {
    }
}
