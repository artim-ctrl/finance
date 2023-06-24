<?php

declare(strict_types = 1);

namespace App\Http\Requests\Calendar\Month\Row;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Max(50)]
        public string $name,
        #[Required]
        public float $amount,
        #[Required]
        #[MapName('currency_id')]
        #[Exists('currencies', 'id')]
        public int $currencyId,
    ) {
    }
}
