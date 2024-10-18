<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\VacationLimitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property User $user
 * @property int $year
 * @property int $limit
 * @property int $days
 * @property int $from_previous_year
 * @property int $to_next_year
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
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    protected static function newFactory(): VacationLimitFactory
    {
        return VacationLimitFactory::new();
    }
}
