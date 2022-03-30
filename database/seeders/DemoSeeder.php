<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\States\VacationRequest\AcceptedByAdministrative;
use Toby\Domain\States\VacationRequest\AcceptedByTechnical;
use Toby\Domain\States\VacationRequest\Approved;
use Toby\Domain\States\VacationRequest\Created;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Domain\VacationDaysCalculator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\VacationRequestActivity;
use Toby\Eloquent\Models\YearPeriod;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory([
            "first_name" => "Jan",
            "last_name" => "Kowalski",
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
            "employment_form" => EmploymentForm::EmploymentContract,
            "position" => "programista",
            "role" => Role::Administrator,
            "employment_date" => Carbon::createFromDate(2021, 12, 31),
            "remember_token" => Str::random(10),
        ])
            ->create();

        User::factory([
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

        User::factory([
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

        User::factory([
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
            "state" => Created::class,
            "from" => Carbon::create($currentYearPeriod->year, 1, 31)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Komentarz do wniosku urlopowego.",
        ])
            ->for($user)
            ->for($user, "creator")
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
            "to" => Created::class,
        ])->for($vacationRequestApproved)
            ->for($user)
            ->create();

        VacationRequestActivity::factory([
            "from" => Created::class,
            "to" => WaitingForTechnical::class,
        ])->for($vacationRequestApproved)
            ->create();

        VacationRequestActivity::factory([
            "from" => WaitingForTechnical::class,
            "to" => AcceptedByTechnical::class,
        ])->for($vacationRequestApproved)
            ->for($technicalApprover)
            ->create();

        VacationRequestActivity::factory([
            "from" => AcceptedByTechnical::class,
            "to" => WaitingForAdministrative::class,
        ])->for($vacationRequestApproved)
            ->create();

        VacationRequestActivity::factory([
            "from" => WaitingForAdministrative::class,
            "to" => AcceptedByAdministrative::class,
        ])->for($vacationRequestApproved)
            ->for($administrativeApprover)
            ->create();

        VacationRequestActivity::factory([
            "from" => AcceptedByAdministrative::class,
            "to" => Approved::class,
        ])->for($vacationRequestApproved)
            ->create();

        $vacationRequestApproved->state = new Approved($vacationRequestApproved);
        $vacationRequestApproved->save();

        /** @var VacationRequest $vacationRequestWaitsForAdminApproval */
        $vacationRequestWaitsForAdminApproval = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Created::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 14)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 14)->toDateString(),
            "comment" => "Komentarz do wniosku urlopowego.",
        ])
            ->for($user)
            ->for($user, "creator")
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
            "to" => Created::class,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->for($user)
            ->create();

        VacationRequestActivity::factory([
            "from" => Created::class,
            "to" => WaitingForTechnical::class,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->create();

        VacationRequestActivity::factory([
            "from" => WaitingForTechnical::class,
            "to" => AcceptedByTechnical::class,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->for($technicalApprover)
            ->create();

        VacationRequestActivity::factory([
            "from" => AcceptedByTechnical::class,
            "to" => WaitingForAdministrative::class,
        ])->for($vacationRequestWaitsForAdminApproval)
            ->create();

        $vacationRequestWaitsForAdminApproval->state = new WaitingForAdministrative($vacationRequestWaitsForAdminApproval);
        $vacationRequestWaitsForAdminApproval->save();

        /** @var VacationRequest $vacationRequestRejected */
        $vacationRequestRejected = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Created::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "comment" => "",
        ])
            ->for($user)
            ->for($user, "creator")
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
            "to" => Created::class,
        ])->for($vacationRequestRejected)
            ->for($user)
            ->create();

        VacationRequestActivity::factory([
            "from" => Created::class,
            "to" => WaitingForTechnical::class,
        ])->for($vacationRequestRejected)
            ->create();

        VacationRequestActivity::factory([
            "from" => WaitingForTechnical::class,
            "to" => Rejected::class,
        ])->for($vacationRequestRejected)
            ->for($technicalApprover)
            ->create();

        $vacationRequestRejected->state = new Rejected($vacationRequestRejected);
        $vacationRequestRejected->save();
    }
}
