<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $date
 * @property string $event_id
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
}
