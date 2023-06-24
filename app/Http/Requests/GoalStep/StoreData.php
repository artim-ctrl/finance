<?php

declare(strict_types = 1);

namespace App\Http\Requests\GoalStep;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Max(255)]
        public string $name,
        #[Required]
        #[Exists('currencies', 'id')]
        public int $estimatedCurrencyId,
        #[Required]
        public float $estimatedAmount,
        #[Required]
        #[Exists('currencies', 'id')]
        public ?int $currencyId = null,
    ) {
    }
}
