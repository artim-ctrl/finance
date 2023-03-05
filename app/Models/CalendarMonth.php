<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $calendar_id
 * @property int $year
 * @property int $month
 *
 * @property Calendar $calendar
 * @property Collection<MonthRow> $rows
 */
class CalendarMonth extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'calendar_id',
        'year',
        'month',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    public function rows(): HasMany
    {
        return $this->hasMany(related: MonthRow::class, foreignKey: 'month_id');
    }
}
