<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $user_id
 * @property int $expense_type_id
 * @property int $balance_id
 * @property float $amount
 * @property Carbon|null $spent_at
 * @property Carbon|null $planned_at
 * @property Carbon $created_at
 *
 * @property User $user
 * @property ExpenseType $type
 * @property Balance $balance
 */
final class Expense extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'expense_type_id',
        'balance_id',
        'amount',
        'spent_at',
        'planned_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'spent_at' => 'date',
        'planned_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }
}
