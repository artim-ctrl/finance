<?php

declare(strict_types = 1);

namespace App\Http\Requests\Exchange;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Exists('balances', 'id')]
        public int $balanceIdFrom,
        #[Required]
        public float $amountFrom,
        #[Required]
        #[Exists('balances', 'id')]
        public int $balanceIdTo,
        #[Required]
        public float $amountTo,
        #[Required]
        #[Date]
        public string $exchangedAt,
    ) {
    }
}
