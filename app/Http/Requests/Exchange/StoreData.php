<?php

namespace App\Http\Requests\Exchange;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreData extends Data
{
    public function __construct(
        public int $balanceIdFrom,
        public float $amountFrom,
        public int $balanceIdTo,
        public float $amountTo,
        #[Date]
        public string $exchangedAt,
    ) {
    }
}
