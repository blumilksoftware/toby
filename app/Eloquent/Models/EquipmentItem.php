<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Carbon\Carbon;
use Database\Factories\EquipmentItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $id_number
 * @property string $name
 * @property bool $is_mobile
 * @property ?int $assignee_id
 * @property ?User $assignee
 * @property Carbon $assigned_at
 * @property Collection $labels
 */
class EquipmentItem extends Model
{
    use HasFactory;

    protected $casts = [
        "assigned_at" => "date",
        "labels" => AsCollection::class,
    ];
    protected $fillable = [
        "id_number",
        "name",
        "is_mobile",
        "labels",
        "assignee_id",
        "assigned_at",
    ];

    public function scopeSearch(Builder $query, ?string $text): Builder
    {
        if ($text === null) {
            return $query;
        }

        return $query
            ->where("id_number", "ILIKE", "%{$text}%")
            ->orWhere("name", "ILIKE", "%{$text}%")
            ->orWhere("labels", "ILIKE", "%{$text}%")
            ->orWhereRelation(
                "assignee",
                fn(Builder $query): Builder => $query
                    ->where("email", "ILIKE", "%{$text}%")
                    ->orWhereRelation(
                        "profile",
                        fn(Builder $query): Builder => $query
                            ->where("first_name", "ILIKE", "%{$text}%")
                            ->orWhere("last_name", "ILIKE", "%{$text}%")
                            ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), "ILIKE", "%{$text}%"),
                    ),
            );
    }

    public function scopeLabels(Builder $query, ?array $labels): Builder
    {
        if ($labels === null) {
            return $query;
        }

        $query->where(function (Builder $query) use ($labels): Builder {
            foreach ($labels as $label) {
                $query->orWhereJsonContains("labels", $label);
            }

            return $query;
        });

        return $query;
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, "assignee_id");
    }

    protected static function newFactory(): EquipmentItemFactory
    {
        return EquipmentItemFactory::new();
    }
}
