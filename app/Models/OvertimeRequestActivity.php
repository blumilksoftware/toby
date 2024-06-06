<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\OvertimeRequestActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Toby\States\OvertimeRequest\OvertimeRequestState;

/**
 * @property int $id
 * @property OvertimeRequest $overtimeRequest
 * @property ?User $user
 * @property ?OvertimeRequestState $from
 * @property OvertimeRequestState $to
 */
class OvertimeRequestActivity extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "from" => OvertimeRequestState::class,
        "to" => OvertimeRequestState::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    public function overtimeRequest(): BelongsTo
    {
        return $this->belongsTo(OvertimeRequest::class);
    }

    protected static function newFactory(): OvertimeRequestActivityFactory
    {
        return OvertimeRequestActivityFactory::new();
    }
}
