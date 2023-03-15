<?php

namespace App\Http\Requests\Income;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Max(31)]
        public int $dayReceiving,
        #[Exists('currencies', 'id')]
        public int $currencyId,
        public float $amount,
        #[Min(1)]
        #[Max(12)]
        public ?int $increaseMonth,
        public ?float $increaseAmount,
    ) {
    }
}
