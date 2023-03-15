<?php

namespace App\Http\Requests\GoalStep;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Exists('currencies', 'id')]
        public int $estimatedCurrencyId,
        public float $estimatedAmount,
        #[Exists('currencies', 'id')]
        public int $currencyId,
    ) {
    }
}
