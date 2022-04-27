<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\VacationRequestActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Toby\Domain\States\VacationRequest\VacationRequestState;

/**
 * @property int $id
 * @property VacationRequest $vacationRequest
 * @property ?User $user
 * @property ?VacationRequestState $from
 * @property VacationRequestState $to
 */
class VacationRequestActivity extends Model
{
    use HasFactory;

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

    protected static function newFactory(): VacationRequestActivityFactory
    {
        return VacationRequestActivityFactory::new();
    }
}
