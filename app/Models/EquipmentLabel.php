<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\EquipmentLabelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property Collection $items
 */
class EquipmentLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentItem::class, "equipment_items_labels");
    }

    protected static function newFactory(): EquipmentLabelFactory
    {
        return EquipmentLabelFactory::new();
    }
}
