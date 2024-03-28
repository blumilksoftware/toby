<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\KeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Toby\Notifications\Notifiable as NotifiableInterface;

/**
 * @property int $id
 * @property User $user
 */
class Key extends Model implements NotifiableInterface
{
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    public function routeNotificationForSlack(): string
    {
        return config("services.slack.default_channel");
    }

    protected static function newFactory(): KeyFactory
    {
        return KeyFactory::new();
    }
}
