<?php

declare(strict_types=1);

namespace Toby\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Toby\Enums\EmploymentForm;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $avatar
 * @property EmploymentForm $employment_form
 * @property Carbon $employment_date
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        "name",
        "email",
        "avatar",
        "employment_form",
        "employment_date",
    ];

    protected $casts = [
        "employment_form" => EmploymentForm::class,
        "employment_date" => "datetime",
    ];

    protected $hidden = [
        "remember_token",
    ];

    public function scopeSearch(Builder $query, ?string $text): Builder
    {
        if ($text === null) {
            return $query;
        }

        return $query
            ->where("name", "LIKE", "%{$text}%")
            ->orWhere("email", "LIKE", "%{$text}%");
    }
}
