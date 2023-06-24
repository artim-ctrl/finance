<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $month_id
 * @property string $name
 * @property float $amount
 * @property int $currency_id
 *
 * @property CalendarMonth $month
 * @property Currency $currency
 */
final class MonthRow extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'month_id',
        'name',
        'amount',
        'currency_id',
    ];

    public function month(): BelongsTo
    {
        return $this->belongsTo(related: CalendarMonth::class, foreignKey: 'month_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
