<?php

namespace App\Http\Requests\Expense;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StoreData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Max(2048)]
        public ?string $description,
        public int $expenseTypeId,
        public int $balanceId,
        public float $amount,
        #[Date]
        public ?string $spentAt,
        #[Date]
        public ?string $plannedAt,
        public bool $forHistory,
    ) {
    }
}
