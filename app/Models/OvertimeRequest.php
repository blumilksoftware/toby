<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\OvertimeRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;
use Toby\Enums\SettlementType;
use Toby\States\OvertimeRequest\OvertimeRequestState;

/**
 * @property int $id
 * @property string $name
 * @property OvertimeRequestState $state
 * @property Carbon $from
 * @property Carbon $to
 * @property integer $hours
 * @property SettlementType $settlement_type
 * @property boolean $settled
 * @property string $comment
 * @property User $user
 * @property User $creator
 * @property YearPeriod $yearPeriod
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OvertimeRequest extends Model
{
    use HasFactory;
    use HasStates;

    protected $guarded = [];
    protected $casts = [
        "state" => OvertimeRequestState::class,
        "from" => "datetime",
        "to" => "datetime",
        "settled" => "boolean",
        "settlement_type" => SettlementType::class,
    ];
    protected $perPage = 50;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
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
        return $this->hasMany(OvertimeRequestActivity::class);
    }

    protected static function newFactory(): OvertimeRequestFactory
    {
        return OvertimeRequestFactory::new();
    }
}
