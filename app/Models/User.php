<?php

declare(strict_types=1);

namespace Toby\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Socialite\Two\User as SocialUser;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $avatar
 * @property string $google_id
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        "name",
        "email",
    ];

    protected $hidden = [
        "remember_token",
    ];

    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    public function syncGoogleData(SocialUser $user): void
    {
        $this->name = $user->getName();
        $this->avatar = $user->getAvatar();
        $this->google_id = $user->getId();

        $this->save();
    }
}
