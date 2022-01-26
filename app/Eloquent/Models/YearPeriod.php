<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\YearPeriodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $year
 * @property Collection $vacationLimits
 * @property Collection $holidays
 */
class YearPeriod extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function current(): ?static
    {
        return static::findByYear(Carbon::now()->year);
    }

    public static function findByYear(int $year): ?static
    {
        /** @var YearPeriod $year */
        $year = static::query()->where("year", $year)->first();

        return $year;
    }

    public function vacationLimits(): HasMany
    {
        return $this->hasMany(VacationLimit::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    protected static function newFactory(): YearPeriodFactory
    {
        return YearPeriodFactory::new();
    }
}
