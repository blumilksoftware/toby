<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Rackbeat\UIAvatars\HasAvatar;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $avatar
 * @property string $position
 * @property Role $role
 * @property EmploymentForm $employment_form
 * @property Carbon $employment_date
 * @property Collection $vacationLimits
 * @property Collection $vacationRequests
 * @property Collection $vacations
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasAvatar;

    protected $guarded = [];

    protected $casts = [
        "role" => Role::class,
        "employment_form" => EmploymentForm::class,
        "employment_date" => "date",
    ];

    protected $hidden = [
        "remember_token",
    ];

    public function vacationLimits(): HasMany
    {
        return $this->hasMany(VacationLimit::class);
    }

    public function vacationRequests(): HasMany
    {
        return $this->hasMany(VacationRequest::class);
    }

    public function createdVacationRequests(): HasMany
    {
        return $this->hasMany(VacationRequest::class, "creator_id");
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function scopeSearch(Builder $query, ?string $text): Builder
    {
        if ($text === null) {
            return $query;
        }

        return $query
            ->where("first_name", "ILIKE", $text)
            ->orWhere("last_name", "ILIKE", $text)
            ->orWhere("email", "ILIKE", $text);
    }

    public function getAvatar(): string
    {
        $colors = config("colors");

        return $this->getAvatarGenerator()
            ->backgroundColor($colors[strlen($this->fullname) % count($colors)])
            ->image();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    protected function getAvatarNameKey(): string
    {
        return "fullName";
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
