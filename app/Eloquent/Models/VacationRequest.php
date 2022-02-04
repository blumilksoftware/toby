<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\VacationRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;

/**
 * @property int $id
 * @property VacationType $type
 * @property VacationRequestState $state
 * @property Carbon $from
 * @property Carbon $to
 * @property string $comment
 * @property User $user
 * @property YearPeriod $yearPeriod
 * @property Collection $activities
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VacationRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "type" => VacationType::class,
        "state" => VacationRequestState::class,
        "from" => "date",
        "to" => "date",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function yearPeriod(): BelongsTo
    {
        return $this->belongsTo(YearPeriod::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(VacationRequestActivity::class);
    }

    public function changeStateTo(VacationRequestState $state): void
    {
        $this->state = $state;

        $this->save();
    }

    public function scopeStates(Builder $query, array $states): Builder
    {
        return $query->whereIn("state", $states);
    }

    protected static function newFactory(): VacationRequestFactory
    {
        return VacationRequestFactory::new();
    }
}
