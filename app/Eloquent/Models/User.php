<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\Notifiable as NotifiableInterface;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property Role $role
 * @property Profile $profile
 * @property Collection $vacationLimits
 * @property Collection $vacationRequests
 * @property Collection $vacations
 * @property Collection $keys
 */
class User extends Authenticatable implements NotifiableInterface
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    protected $guarded = [];
    protected $casts = [
        "role" => Role::class,
        "last_active_at" => "datetime",
        "employment_form" => EmploymentForm::class,
        "employment_date" => "date",
    ];
    protected $hidden = [
        "remember_token",
    ];
    protected $with = [
        "profile",
    ];
    protected $perPage = 50;

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

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

    public function keys(): HasMany
    {
        return $this->hasMany(Key::class);
    }

    public function hasVacationLimit(YearPeriod $yearPeriod): bool
    {
        return $this->vacationLimits()
            ->whereBelongsTo($yearPeriod)
            ->whereNotNull("days")
            ->exists();
    }

    public function scopeSearch(Builder $query, ?string $text): Builder
    {
        if ($text === null) {
            return $query;
        }

        return $query
            ->where("email", "ILIKE", "%{$text}%")
            ->orWhereRelation(
                "profile",
                fn(Builder $query): Builder => $query
                    ->where("first_name", "ILIKE", "%{$text}%")
                    ->orWhere("last_name", "ILIKE", "%{$text}%"),
            );
    }

    public function scopeOrderByProfileField(Builder $query, string $field, string $direction = "asc"): Builder
    {
        $profileQuery = Profile::query()->select($field)->whereColumn("users.id", "profiles.user_id");

        return $query->orderBy($profileQuery, $direction);
    }

    public function scopeWithVacationLimitIn(Builder $query, YearPeriod $yearPeriod): Builder
    {
        return $query->whereRelation(
            "vacationlimits",
            fn(Builder $query): Builder => $query
                ->whereBelongsTo($yearPeriod)
                ->whereNotNull("days"),
        );
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            "active" => $query,
            "inactive" => $query->onlyTrashed(),
            "all" => $query->withTrashed(),
            default => $query,
        };
    }

    public function upcomingBirthday(): ?Carbon
    {
        if (!$this->profile->birthday) {
            return null;
        }

        $today = Carbon::today();

        $birthday = $this->profile->birthday->setYear($today->year);

        if ($birthday->diffInDays(absolute: false) > 0) {
            $birthday->setYear($today->year + 1);
        }

        return $birthday;
    }

    public function seniority(): string
    {
        return $this->profile->employment_date->longAbsoluteDiffForHumans(Carbon::today(), 2);
    }

    public function routeNotificationForSlack()
    {
        return $this->profile->slack_id;
    }

    public function scopeSortForEmployeesMilestones(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            //            "birthday-asc"  TO DO
            //            "birthday-desc" TO DO
            "seniority-asc" => $query->orderByProfileField("employment_date", "asc"),
            "seniority-desc" => $query->orderByProfileField("employment_date", "desc"),
            default => $query
                ->orderByProfileField("last_name")
                ->orderByProfileField("first_name"),
        };
    }

    public function isWorkAnniversaryToday(): bool
    {
        $today = Carbon::now();

        $workAnniversary = $this->profile->employment_date->setYear($today->year);

        return $workAnniversary->isToday();
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
