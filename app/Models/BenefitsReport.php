<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\BenefitsReportFactory;
use Illuminate\Database\Eloquent\Builder;
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
class BenefitsReport extends Model
{
    use HasFactory;

    protected $table = "reports";
    protected $guarded = [];
    protected $casts = [
        "benefits" => "array",
        "users" => "array",
        "data" => "array",
    ];

    public function scopeSearch(Builder $query, ?string $text): Builder
    {
        if ($text === null) {
            return $query;
        }

        return $query
            ->where("name", "ILIKE", "%{$text}%");
    }

    protected static function booted(): void
    {
        static::addGlobalScope("withoutAssignedBenefitReport", fn(Builder $builder): Builder => $builder->whereNotNull("committed_at"));
    }

    protected static function newFactory(): BenefitsReportFactory
    {
        return BenefitsReportFactory::new();
    }
}
