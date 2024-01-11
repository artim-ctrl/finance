<?php

declare(strict_types = 1);

namespace App\Http\Requests\Balance;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Exists('currencies', 'id')]
        public int $currencyId,
        #[Required]
        public float $amount,
    ) {
    }

    /**
     * @param ValidationContext $context
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'currency_id' => [
                Rule::unique(table: 'balances', column: 'currency_id')->where(column: 'user_id', value: auth()->id()),
            ],
        ];
    }
}
