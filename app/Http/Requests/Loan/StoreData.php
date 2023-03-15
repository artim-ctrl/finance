<?php

namespace App\Http\Requests\Loan;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
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
        public float $amount,
        #[Exists('currencies', 'id')]
        public int $currencyId,
        public int $term,
        #[Date]
        public string $firstPayment,
    ) {
    }
}
