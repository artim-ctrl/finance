<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $currency_id
 * @property float $amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
