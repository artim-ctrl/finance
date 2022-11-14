<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $currency_id_from
 * @property float $amount_from
 * @property int $currency_id_to
 * @property float $amount_to
 * @property Carbon $exchanged_at
 *
 * @property User $user
 * @property Currency $currencyFrom
 * @property Currency $currencyTo
 */
class Exchange extends Model
{
    use HasFactory;

    /** @var array<string> */
    protected $fillable = [
        'user_id',
        'currency_id_from',
        'amount_from',
        'currency_id_to',
        'amount_to',
        'exchanged_at',
    ];

    /** @var array<string> */
    protected $casts = [
        'exchanged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currencyFrom(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id_from');
    }

    public function currencyTo(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id_to');
    }
}
