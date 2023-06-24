<?php

declare(strict_types = 1);

namespace App\Http\Requests\Loan;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
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
        public float $amount,
        #[Required]
        #[Exists('currencies', 'id')]
        public int $currencyId,
        #[Required]
        public int $term,
        #[Required]
        #[Date]
        public string $firstPayment,
    ) {
    }
}
