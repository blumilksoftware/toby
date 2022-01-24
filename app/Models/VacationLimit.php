<?php

declare(strict_types=1);

namespace Toby\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property User $user
 * @property YearPeriod $yearPeriod
 * @property int $days
 */
class VacationLimit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hasVacation(): bool
    {
        return $this->days !== null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function yearPeriod(): BelongsTo
    {
        return $this->belongsTo(YearPeriod::class);
    }

    public function scopeOrderByUserField(Builder $query, string $field): Builder
    {
        $userQuery = User::query()->select($field)->whereColumn("vacation_limits.user_id", "users.id");

        return $query->orderBy($userQuery);
    }
}
