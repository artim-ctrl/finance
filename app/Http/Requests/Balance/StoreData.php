<?php

namespace App\Http\Requests\Balance;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreData extends Data
{
    public function __construct(
        #[Exists('currencies', 'id')]
        public int $currencyId,
        public float $amount,
    ) {
    }
}
