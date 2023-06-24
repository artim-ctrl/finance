<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property float $amount
 * @property int $currency_id
 * @property int $term
 * @property Carbon $first_payment
 * @property int $user_id
 *
 * @property Currency $currency
 * @property User $user
 */
final class Loan extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'amount',
        'currency_id',
        'term',
        'first_payment',
        'user_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'first_payment' => 'date',
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
