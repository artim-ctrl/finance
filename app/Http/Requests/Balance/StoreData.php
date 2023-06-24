<?php

declare(strict_types = 1);

namespace App\Http\Requests\Balance;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Exists('currencies', 'id')]
        public int $currencyId,
        #[Required]
        public float $amount,
    ) {
    }
}
