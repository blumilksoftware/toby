<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Toby\Domain\Enums\VacationRequestState;

/**
 * @property int $id
 * @property VacationRequest $vacationRequest
 * @property ?User $user
 * @property ?VacationRequestState $from
 * @property VacationRequestState $to
 */
class VacationRequestActivity extends Model
{
    protected $guarded = [];

    protected $casts = [
        "from" => VacationRequestState::class,
        "to" => VacationRequestState::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacationRequest(): BelongsTo
    {
        return $this->belongsTo(VacationRequest::class);
    }
}