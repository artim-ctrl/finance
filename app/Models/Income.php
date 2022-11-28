<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property int $day_receiving
 * @property int $currency_id
 * @property float $amount
 * @property int $user_id
 * @property int|null $increase_month
 * @property float|null $increase_amount
 *
 * @property Currency $currency
 * @property User $user
 */
class Income extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'day_receiving',
        'currency_id',
        'amount',
        'user_id',
        'increase_month',
        'increase_amount',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
