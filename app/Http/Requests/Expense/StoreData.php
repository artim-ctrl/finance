<?php

declare(strict_types = 1);

namespace App\Http\Requests\Expense;

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
        #[Max(2048)]
        public ?string $description,
        #[Required]
        #[Exists('expense_types', 'id')]
        public int $expenseTypeId,
        #[Required]
        #[Exists('balances', 'id')]
        public int $balanceId,
        #[Required]
        public float $amount,
        #[Required]
        #[Date]
        public ?string $spentAt,
        #[Required]
        #[Date]
        public ?string $plannedAt,
        #[Required]
        public bool $forHistory,
    ) {
        if (null === $this->spentAt) {
            $this->spentAt = now()->format(format: 'Y-m-d H:i:s');
        }
    }
}
