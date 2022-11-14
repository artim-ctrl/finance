<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $currency_id
 * @property float $amount
 *
 * @property User $user
 * @property Currency $currency
 */
class Balance extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'currency_id',
        'amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
