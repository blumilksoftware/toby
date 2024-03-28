<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\HolidayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $date
 * @property YearPeriod $yearPeriod
 */
class Holiday extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "date" => "date",
    ];

    public function yearPeriod(): BelongsTo
    {
        return $this->belongsTo(YearPeriod::class);
    }

    protected static function newFactory(): HolidayFactory
    {
        return HolidayFactory::new();
    }
}
