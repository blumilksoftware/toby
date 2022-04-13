<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\KeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property User $owner
 * @property User $previousOwner
 */
class Key extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "owner_id");
    }

    public function previousOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, "previous_owner_id");
    }

    protected static function newFactory(): KeyFactory
    {
        return KeyFactory::new();
    }
}
