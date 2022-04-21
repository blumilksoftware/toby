<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\KeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property User $user
 */
class Key extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): KeyFactory
    {
        return KeyFactory::new();
    }
}
