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
 * @property boolean $skip_flow
 * @property User $user
 * @property User $creator
 * @property YearPeriod $yearPeriod
 * @property Collection $activities
 * @property Collection $vacations
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "creator_id");
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

    public function changeStateTo(VacationRequestState $state): void
    {
        $this->state = $state;

        $this->save();
    }

    public function shouldSkipFlow(): bool
    {
        return $this->skip_flow;
    }

    public function scopeStates(Builder $query, array $states): Builder
    {
        return $query->whereIn("state", $states);
    }

    public function scopeNoStates(Builder $query, array $states): Builder
    {
        return $query->whereNotIn("state", $states);
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
