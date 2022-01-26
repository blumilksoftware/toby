<?php

namespace Toby\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Toby\Enums\VacationType;

/**
 * @property int $id
 * @property VacationType $type
 * @property Carbon $from
 * @property Carbon $to
 * @property string $comment
 * @property User $user
 */
class VacationRequest extends Model
{
    use HasFactory;

    protected $casts = [
        "type" => VacationType::class,
        "from" => "date",
        "to" => "date",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
