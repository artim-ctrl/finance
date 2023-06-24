<?php

declare(strict_types = 1);

namespace App\Http\Requests\Income;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
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
        #[Max(31)]
        public int $dayReceiving,
        #[Required]
        #[Exists('currencies', 'id')]
        public int $currencyId,
        #[Required]
        public float $amount,
        #[Required]
        #[Min(1)]
        #[Max(12)]
        public ?int $increaseMonth,
        #[Required]
        public ?float $increaseAmount,
    ) {
    }
}
