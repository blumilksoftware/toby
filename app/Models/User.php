<?php

declare(strict_types=1);

namespace Toby\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Toby\Enums\FormOfEmployment;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $avatar
 * @property FormOfEmployment $employment_form
 * @property Carbon $empoyment_start_date
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $perPage = 10;

    protected $fillable = [
        "name",
        "email",
        "avatar",
        "employment_form",
        "employment_start_date",
    ];

    protected $casts = [
        "employment_form" => FormOfEmployment::class,
        "employment_start_date" => "datetime",
    ];

    protected $hidden = [
        "remember_token",
    ];

    public function scopeSearch(Builder $query, ?string $text): Builder
    {
        if ($text == null) {
            return $query;
        }

        return $query
            ->where("name", "LIKE", "%{$text}%")
            ->orWhere("email", "LIKE", "%{$text}%");
    }
}
