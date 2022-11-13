<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property int $goal_id
 * @property int $estimated_currency_id
 * @property float $estimated_amount
 * @property int $currency_id
 * @property float $amount
 *
 * @property Goal $goal
 * @property Currency $estimatedCurrency
 * @property Currency $currency
 */
class GoalStep extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'goal_id',
        'estimated_currency_id',
        'estimated_amount',
        'currency_id',
        'amount',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function estimatedCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
