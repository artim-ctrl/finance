<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\BalanceHistory\ActionEnum;
use App\Enums\BalanceHistory\EntityTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property ActionEnum $action
 * @property int $balance_id
 * @property float $amount_from
 * @property float $amount_to
 * @property EntityTypeEnum $entity_type
 * @property int $entity_id
 * @property Carbon $done_at
 *
 * @property Balance $balance
 * @property Expense|Exchange|Loan|Income $entity
 */
final class BalanceHistory extends Model
{
    use HasTimestamps;

    /** @var string */
    protected $table = 'balance_history';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'action',
        'balance_id',
        'amount_from',
        'amount_to',
        'entity_type',
        'entity_id',
        'done_at',
    ];

    /**
     * @var array<string, class-string|string>
     */
    protected $casts = [
        'action' => ActionEnum::class,
        'entity_type' => EntityTypeEnum::class,
        'done_at' => 'date',
    ];

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }

    public function entity(): BelongsTo
    {
        $entityType = match ($this->entity_type) {
            EntityTypeEnum::EXPENSES => Expense::class,
            EntityTypeEnum::EXCHANGES => Exchange::class,
            EntityTypeEnum::LOANS => Loan::class,
            EntityTypeEnum::INCOMES => Income::class,
        };

        return $this->belongsTo($entityType);
    }
}
