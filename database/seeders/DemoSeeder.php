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
use Toby\Domain\WorkDaysCalculator;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\BenefitsReport;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\Resume;
use Toby\Eloquent\Models\Technology;
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
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
            "role" => Role::Administrator,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Jan",
                "last_name" => "Kowalski",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "programista",
                "employment_date" => Carbon::createFromDate(2021, 12, 31),
            ])
            ->create();

        User::factory([
            "email" => "anna.nowak@example.com",
            "role" => Role::Employee,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Anna",
                "last_name" => "Nowak",
                "employment_form" => EmploymentForm::CommissionContract,
                "position" => "tester",
                "employment_date" => Carbon::createFromDate(2021, 5, 10),
            ])
            ->create();

        User::factory([
            "email" => "tola.sawicka@example.com",
            "role" => Role::Employee,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Tola",
                "last_name" => "Sawicka",
                "employment_form" => EmploymentForm::B2bContract,
                "position" => "programista",
                "employment_date" => Carbon::createFromDate(2021, 1, 4),
            ])
            ->create();

        $technicalApprover = User::factory([
            "email" => "maciej.ziolkowski@example.com",
            "role" => Role::TechnicalApprover,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Maciej",
                "last_name" => "Ziółkowski",
                "employment_form" => EmploymentForm::BoardMemberContract,
                "position" => "programista",
                "employment_date" => Carbon::createFromDate(2021, 1, 4),
            ])
            ->create();

        $administrativeApprover = User::factory([
            "email" => "katarzyna.zajac@example.com",
            "role" => Role::AdministrativeApprover,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Katarzyna",
                "last_name" => "Zając",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "dyrektor",
                "employment_date" => Carbon::createFromDate(2021, 1, 4),
            ])
            ->create();

        User::factory([
            "email" => "milosz.borowski@example.com",
            "role" => Role::Administrator,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Miłosz",
                "last_name" => "Borowski",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "administrator",
                "employment_date" => Carbon::createFromDate(2021, 1, 4),
            ])
            ->create();

        $users = User::all();

        $year = 2021;

        YearPeriod::factory()
            ->count(3)
            ->sequence(
                [
                    "year" => Carbon::createFromDate($year)->year,
                ],
                [
                    "year" => Carbon::createFromDate($year + 1)->year,
                ],
                [
                    "year" => Carbon::createFromDate($year + 2)->year,
                ],
            )
            ->afterCreating(function (YearPeriod $yearPeriod) use ($users): void {
                foreach ($users as $user) {
                    VacationLimit::factory([
                        "days" => $user->profile->employment_form === EmploymentForm::EmploymentContract ? 26 : null,
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
                $days = app(WorkDaysCalculator::class)->calculateDays(
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
                $days = app(WorkDaysCalculator::class)->calculateDays(
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

        $vacationRequestWaitsForAdminApproval->state = new WaitingForAdministrative(
            $vacationRequestWaitsForAdminApproval,
        );
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
                $days = app(WorkDaysCalculator::class)->calculateDays(
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

        foreach ($users as $user) {
            Key::factory()
                ->for($user)
                ->create();
        }

        Technology::factory()->createMany([
            ["name" => "Laravel"],
            ["name" => "Symfony"],
            ["name" => "CakePHP"],
            ["name" => "PHP"],
            ["name" => "Livewire"],
            ["name" => "Inertia"],
            ["name" => "Vue"],
            ["name" => "Javascript"],
            ["name" => "Redis"],
            ["name" => "AWS"],
            ["name" => "Tailwind"],
            ["name" => "CSS"],
            ["name" => "PHPUnit"],
            ["name" => "Cypress"],
            ["name" => "Behat"],
            ["name" => "Pest"],
            ["name" => "Golang"],
        ]);

        foreach ($users as $user) {
            Resume::factory()
                ->for($user)
                ->create();
        }

        Resume::factory()
            ->count(3)
            ->create();

        Benefit::factory()->createMany([
            ["name" => "Parking", "companion" => false],
            ["name" => "Allianz opieka medyczna", "companion" => false],
            ["name" => "Allianz opieka medyczna, partner", "companion" => true],
            ["name" => "Ubezpieczenie grupowe Allianz", "companion" => false],
            ["name" => "Ubezpieczenie grupowe Allianz, partner", "companion" => true],
            ["name" => "Ubezpieczenie grupowe Nationale-Nederlanden", "companion" => false],
            ["name" => "Karta MultiSport", "companion" => false],
        ]);

        BenefitsReport::factory()->create([
            "name" => "current",
            "users" => null,
            "benefits" => null,
            "data" => null,
            "committed_at" => null,
        ]);
    }
}
