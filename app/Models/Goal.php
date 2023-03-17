<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property Collection<GoalStep> $steps
 */
class Goal extends Model
{
    use HasTimestamps;
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(GoalStep::class);
    }
}
