<?php

namespace App\Http\Requests\Calendar\Month\Row;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class StoreData extends Data
{
    public function __construct(
        #[Max(50)]
        public string $name,
        public float $amount,
        #[MapName('currency_id')]
        #[Exists('currencies', 'id')]
        public int $currencyId,
    ) {
    }
}
