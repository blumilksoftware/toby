<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Domain\VacationRequestStatesRetriever;

/**
 * @property int $id
 * @property Carbon $date
 * @property User $user
 * @property VacationRequest $vacationRequest
 * @property YearPeriod $yearPeriod
 */
class Vacation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        "date" => "date",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacationRequest(): BelongsTo
    {
        return $this->belongsTo(VacationRequest::class);
    }

    public function yearPeriod(): BelongsTo
    {
        return $this->belongsTo(YearPeriod::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereRelation(
            "vacationRequest",
            fn(Builder $query): Builder => $query->states(VacationRequestStatesRetriever::successStates()),
        );
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereRelation(
            "vacationRequest",
            fn(Builder $query): Builder => $query->states(VacationRequestStatesRetriever::pendingStates()),
        );
    }

    public function scopeWhereTypes(Builder $query, Collection $types): Builder
    {
        return $query->whereRelation(
            "vacationRequest",
            fn(Builder $query): Builder => $query->whereIn("type", $types),
        );
    }
}
