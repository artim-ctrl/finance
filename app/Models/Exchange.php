<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TODO: Do i need an actions table?
 *
 * @property int $id
 * @property int $user_id
 * @property int $balance_id_from
 * @property float $amount_from
 * @property int $balance_id_to
 * @property float $amount_to
 * @property Carbon $exchanged_at
 *
 * @property User $user
 * @property Balance $balanceFrom
 * @property Balance $balanceTo
 */
class Exchange extends Model
{
    use HasFactory;

    /** @var array<string> */
    protected $fillable = [
        'user_id',
        'balance_id_from',
        'amount_from',
        'balance_id_to',
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

    public function balanceFrom(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'balance_id_from');
    }

    public function balanceTo(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'balance_id_to');
    }
}
