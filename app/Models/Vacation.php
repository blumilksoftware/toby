<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\VacationFactory;
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
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    public function vacationRequest(): BelongsTo
    {
        return $this->belongsTo(VacationRequest::class);
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

    protected static function newFactory(): VacationFactory
    {
        return VacationFactory::new();
    }
}
