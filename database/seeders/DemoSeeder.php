<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\VacationDaysCalculator;
use Toby\Eloquent\Helpers\UserAvatarGenerator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\VacationRequestActivity;
use Toby\Eloquent\Models\YearPeriod;

class DemoSeeder extends Seeder
{
    public function __construct(
        protected UserAvatarGenerator $avatarGenerator,
    ) {
    }

    public function run(): void
    {
        User::unsetEventDispatcher();
        YearPeriod::unsetEventDispatcher();
        VacationRequest::unsetEventDispatcher();

        $employee1 = User::factory([
            "first_name" => "Jan",
            "last_name" => "Kowalski",
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
            "employment_form" => EmploymentForm::EmploymentContract,
            "position" => "programista",
            "role" => Role::Employee,
            "employment_date" => Carbon::createFromDate(2021, 12, 31),
            "remember_token" => Str::random(10),
        ])
            ->create();

        $employee2 = User::factory([
            "first_name" => "Anna",
            "last_name" => "Nowak",
            "email" => "anna.nowak@example.com",
            "employment_form" => EmploymentForm::CommissionContract,
            "position" => "tester",
            "role" => Role::Employee,
            "employment_date" => Carbon::createFromDate(2021, 5, 10),
            "remember_token" => Str::random(10),
        ])
            ->create();

        $employee3 = User::factory([
            "first_name" => "Tola",
            "last_name" => "Sawicka",
            "email" => "tola.sawicka@example.com",
            "employment_form" => EmploymentForm::B2bContract,
            "position" => "programista",
            "role" => Role::Employee,
            "employment_date" => Carbon::createFromDate(2021, 1, 4),
            "remember_token" => Str::random(10),
        ])
            ->create();

        $technicalApprover = User::factory([
            "first_name" => "Maciej",
            "last_name" => "Ziółkowski",
            "email" => "maciej.ziolkowski@example.com",
            "employment_form" => EmploymentForm::BoardMemberContract,
            "position" => "programista",
            "role" => Role::TechnicalApprover,
            "employment_date" => Carbon::createFromDate(2021, 1, 4),
            "remember_token" => Str::random(10),
        ])
            ->create();

        $administrativeApprover = User::factory([
            "first_name" => "Katarzyna",
            "last_name" => "Zając",
            "email" => "katarzyna.zajac@example.com",
            "employment_form" => EmploymentForm::EmploymentContract,
            "position" => "dyrektor",
            "role" => Role::AdministrativeApprover,
            "employment_date" => Carbon::createFromDate(2021, 1, 4),
            "remember_token" => Str::random(10),
        ])
            ->create();

        $admin = User::factory([
            "first_name" => "Miłosz",
            "last_name" => "Borowski",
            "email" => "milosz.borowski@example.com",
            "employment_form" => EmploymentForm::EmploymentContract,
            "position" => "administrator",
            "role" => Role::Administrator,
            "employment_date" => Carbon::createFromDate(2021, 1, 4),
            "remember_token" => Str::random(10),
        ])
            ->create();

        $users = User::all();

        $this->generateAvatarsForUsers($users);

        $year = 2021;

        YearPeriod::factory()
            ->count(2)
            ->sequence(
                [
                    "year" => Carbon::createFromDate($year)->year,
                ],
                [
                    "year" => Carbon::createFromDate($year + 1)->year,
                ],
            )
            ->afterCreating(function (YearPeriod $yearPeriod) use ($users): void {
                foreach ($users as $user) {
                    VacationLimit::factory([
                        "days" => $user->employment_form === EmploymentForm::EmploymentContract ? 26 : null,
                    ])
                        ->for($yearPeriod)
                        ->for($user)
                        ->create();
                }
            })
            ->afterCreating(function (YearPeriod $yearPeriod): void {
                $polishHolidaysRetriever = new PolishHolidaysRetriever();

                foreach ($polishHolidaysRetriever->getForYearPeriod($yearPeriod) as $holiday) {
                    $yearPeriod->holidays()->create([
                        "name" => $holiday["name"],
                        "date" => $holiday["date"],
                    ]);
                }
            })
            ->create();

        $currentYearPeriod = YearPeriod::query()->where("year", 2022)->first();

        /** @var VacationRequest $vacationRequestApproved */
        $vacationRequestApproved = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 1, 31)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Komentarz do wniosku urlopowego.",
        ])
            ->for($employee1)
            ->for($employee1, "creator")
            ->for($currentYearPeriod)
            ->afterCreating(function (VacationRequest $vacationRequest): void {
                $days = app(VacationDaysCalculator::class)->calculateDays(
                    $vacationRequest->yearPeriod,
                    $vacationRequest->from,
                    $vacationRequest->to,
                );

                foreach ($days as $day) {
                    $vacationRequest->vacations()->create([
                        "date" => $day,
                        "user_id" => $vacationRequest->user->id,
                        "year_period_id" => $vacationRequest->yearPeriod->id,
                    ]);
                }
            })
            ->create();

        VacationRequestActivity::factory([
            "from" => null,
            "to" => VacationRequestState::Created,
        ])->for($vacationRequestApproved)
            ->for($employee1)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::Created,
            "to" => VacationRequestState::WaitingForTechnical,
        ])->for($vacationRequestApproved)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::WaitingForTechnical,
            "to" => VacationRequestState::AcceptedByTechnical,
        ])->for($vacationRequestApproved)
            ->for($technicalApprover)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::AcceptedByTechnical,
            "to" => VacationRequestState::WaitingForAdministrative,
        ])->for($vacationRequestApproved)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::WaitingForAdministrative,
            "to" => VacationRequestState::AcceptedByAdministrative,
        ])->for($vacationRequestApproved)
            ->for($administrativeApprover)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::AcceptedByAdministrative,
            "to" => VacationRequestState::Approved,
        ])->for($vacationRequestApproved)
            ->create();

        $vacationRequestApproved->changeStateTo(VacationRequestState::Approved);

        /** @var VacationRequest $vacationRequestWaitsForAdminApproval */
        $vacationRequestWaitsForAdminApproval = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 2, 14)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 14)->toDateString(),
            "comment" => "Komentarz do wniosku urlopowego.",
        ])
            ->for($employee1)
            ->for($employee1, "creator")
            ->for($currentYearPeriod)
            ->afterCreating(function (VacationRequest $vacationRequest): void {
                $days = app(VacationDaysCalculator::class)->calculateDays(
                    $vacationRequest->yearPeriod,
                    $vacationRequest->from,
                    $vacationRequest->to,
                );

                foreach ($days as $day) {
                    $vacationRequest->vacations()->create([
                        "date" => $day,
                        "user_id" => $vacationRequest->user->id,
                        "year_period_id" => $vacationRequest->yearPeriod->id,
                    ]);
                }
            })
            ->create();

        VacationRequestActivity::factory([
            "from" => null,
            "to" => VacationRequestState::Created,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->for($employee1)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::Created,
            "to" => VacationRequestState::WaitingForTechnical,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::WaitingForTechnical,
            "to" => VacationRequestState::AcceptedByTechnical,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->for($technicalApprover)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::AcceptedByTechnical,
            "to" => VacationRequestState::WaitingForAdministrative,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->create();

        $vacationRequestWaitsForAdminApproval->changeStateTo(VacationRequestState::WaitingForAdministrative);

        /** @var VacationRequest $vacationRequestRejected */
        $vacationRequestRejected = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "comment" => "",
        ])
            ->for($employee1)
            ->for($employee1, "creator")
            ->for($currentYearPeriod)
            ->afterCreating(function (VacationRequest $vacationRequest): void {
                $days = app(VacationDaysCalculator::class)->calculateDays(
                    $vacationRequest->yearPeriod,
                    $vacationRequest->from,
                    $vacationRequest->to,
                );

                foreach ($days as $day) {
                    $vacationRequest->vacations()->create([
                        "date" => $day,
                        "user_id" => $vacationRequest->user->id,
                        "year_period_id" => $vacationRequest->yearPeriod->id,
                    ]);
                }
            })
            ->create();

        VacationRequestActivity::factory([
            "from" => null,
            "to" => VacationRequestState::Created,
        ])->for($vacationRequestRejected)
            ->for($employee1)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::Created,
            "to" => VacationRequestState::WaitingForTechnical,
        ])->for($vacationRequestRejected)
            ->create();

        VacationRequestActivity::factory([
            "from" => VacationRequestState::WaitingForTechnical,
            "to" => VacationRequestState::Rejected,
        ])->for($vacationRequestRejected)
            ->for($technicalApprover)
            ->create();

        $vacationRequestRejected->changeStateTo(VacationRequestState::Rejected);
    }

    protected function generateAvatarsForUsers(Collection $users): void
    {
        foreach ($users as $user) {
            $user->saveAvatar($this->avatarGenerator->generateFor($user));
        }
    }
}
