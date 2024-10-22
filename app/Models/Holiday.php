<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\HolidayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $date
 */
class Holiday extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "date" => "date",
    ];

    protected static function newFactory(): HolidayFactory
    {
        return HolidayFactory::new();
    }
}
