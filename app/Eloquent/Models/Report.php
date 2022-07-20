<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property array $benefits
 * @property array $users
 * @property array $data
 * @property Carbon $committed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Report extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "benefits" => "array",
        "users" => "array",
        "data" => "array",
    ];

    protected static function newFactory(): ReportFactory
    {
        return ReportFactory::new();
    }
}
