<?php

declare(strict_types = 1);

namespace App\Http\Requests\ExpenseType;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class StoreData extends Data
{
    public function __construct(
        #[Required]
        #[Max(255)]
        public string $name,
    ) {
    }

    /**
     * @param ValidationContext $context
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => Rule::unique(table: 'expense_types', column: 'name')->where(column: 'user_id', value: auth()->id()),
        ];
    }
}
