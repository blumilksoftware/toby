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
use Spatie\ModelStates\HasStates;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\States\VacationRequest\VacationRequestState;

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
 * @property Collection $vacations
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VacationRequest extends Model
{
    use HasFactory;
    use HasStates;

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

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function scopeStates(Builder $query, VacationRequestState|array $states): Builder
    {
        return $query->whereState("state", $states);
    }

    public function scopeNoStates(Builder $query, VacationRequestState|array $states): Builder
    {
        return $query->whereNotState("state", $states);
    }

    public function scopeOverlapsWith(Builder $query, self $vacationRequest): Builder
    {
        return $query->where("from", "<=", $vacationRequest->to)
            ->where("to", ">=", $vacationRequest->from);
    }

    protected static function newFactory(): VacationRequestFactory
    {
        return VacationRequestFactory::new();
    }
}
