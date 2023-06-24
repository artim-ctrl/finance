<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property BalanceHistory $histories
 */
final class Balance extends Model
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

    public function history(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }
}
