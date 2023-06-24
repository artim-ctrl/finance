<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 *
 * @property User $user
 * @property Collection<CalendarMonth> $months
 */
final class Calendar extends Model
{
    use HasTimestamps;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function months(): HasMany
    {
        return $this->hasMany(related: CalendarMonth::class, foreignKey: 'calendar_id');
    }
}
