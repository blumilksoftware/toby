<?php

declare(strict_types=1);

namespace Toby\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $year
 */
class YearPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        "year",
    ];

    public static function current(): ?static
    {
        /** @var YearPeriod $year */
        $year = static::query()->where("year", Carbon::now()->year)->first();

        return $year;
    }
}
