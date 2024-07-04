<?php

declare(strict_types=1);

namespace Toby\Models;

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
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Toby\Enums\Role;
use Toby\Enums\UserHistoryType;
use Toby\Notifications\Notifiable as NotifiableInterface;

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
    use HasApiTokens;

    protected $guarded = [];
    protected $casts = [
        "role" => Role::class,
        "last_active_at" => "datetime",
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

    public function overtimeRequests(): HasMany
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function createdVacationRequests(): HasMany
    {
        return $this->hasMany(VacationRequest::class, "creator_id");
    }

    public function createdOvertimeRequests(): HasMany
    {
        return $this->hasMany(OvertimeRequest::class, "creator_id");
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(UserHistory::class);
    }

    public function lastMedicalExam(): ?UserHistory
    {
        return $this->histories()
            ->where("type", UserHistoryType::MedicalExam)
            ->orderBy("to", "desc")
            ->first();
    }

    public function lastOhsTraining(): ?UserHistory
    {
        return $this->histories()
            ->where("type", UserHistoryType::OhsTraining)
            ->orderBy("to", "desc")
            ->first();
    }

    public function startOfEmploymentInCurrentCompany(): ?UserHistory
    {
        return $this->histories()
            ->where("type", UserHistoryType::Employment)
            ->where("is_employed_at_current_company", true)
            ->orderBy("from", "asc")
            ->first();
    }

    public function endOfEmploymentInCurrentCompany(): ?UserHistory
    {
        return $this->histories()
            ->where("type", UserHistoryType::Employment)
            ->where("is_employed_at_current_company", true)
            ->orderBy("from", "desc")
            ->first();
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
                    ->orWhere("last_name", "ILIKE", "%{$text}%")
                    ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), "ILIKE", "%{$text}%"),
            );
    }

    public function scopeOrderByProfileField(Builder $query, string $field, string $direction = "asc"): Builder
    {
        $profileQuery = Profile::query()->select($field)->whereColumn("users.id", "profiles.user_id");

        return $query->orderBy($profileQuery, $direction);
    }

    public function scopeOrderByUserHistoryField(Builder $query, string $field, string $direction = "asc"): Builder
    {
        $profileQuery = UserHistory::query()->select($field)->whereColumn("users.id", "histories.user_id");

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

        if (((int)$birthday->diffInDays(absolute: false)) > 0) {
            $birthday->setYear($today->year + 1);
        }

        return $birthday;
    }

    public function seniority(): ?string
    {
        $startOfEmploymentInCurrentCompany = $this->startOfEmploymentInCurrentCompany();
        $employmentDate = $startOfEmploymentInCurrentCompany?->from;

        if (!$employmentDate || $employmentDate->isFuture() || $employmentDate->isToday()) {
            return null;
        }

        if ($startOfEmploymentInCurrentCompany->to !== null) {
            $endOfEmploymentInCurrentCompany = $this->endOfEmploymentInCurrentCompany();

            if ($endOfEmploymentInCurrentCompany->to !== null) {
                return $employmentDate->longAbsoluteDiffForHumans($endOfEmploymentInCurrentCompany->to, 2);
            }
        }

        return $employmentDate->longAbsoluteDiffForHumans(Carbon::today(), 2);
    }

    public function routeNotificationForSlack()
    {
        return $this->profile->slack_id;
    }

    public function isWorkAnniversaryToday(): bool
    {
        $today = Carbon::now();

        $employmentDate = $this->profile->employment_date;

        if ($employmentDate->isToday()) {
            return false;
        }

        $workAnniversary = $employmentDate->setYear($today->year);

        return $workAnniversary->isToday();
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
