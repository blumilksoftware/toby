<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\WorkDaysCalculator;
use Toby\Enums\EmploymentForm;
use Toby\Enums\Role;
use Toby\Enums\UserHistoryType;
use Toby\Enums\VacationType;
use Toby\Models\Benefit;
use Toby\Models\BenefitsReport;
use Toby\Models\EquipmentItem;
use Toby\Models\EquipmentLabel;
use Toby\Models\Holiday;
use Toby\Models\Key;
use Toby\Models\Resume;
use Toby\Models\Technology;
use Toby\Models\User;
use Toby\Models\VacationLimit;
use Toby\Models\VacationRequest;
use Toby\Models\VacationRequestActivity;
use Toby\States\VacationRequest\AcceptedByAdministrative;
use Toby\States\VacationRequest\AcceptedByTechnical;
use Toby\States\VacationRequest\Approved;
use Toby\States\VacationRequest\Created;
use Toby\States\VacationRequest\Rejected;
use Toby\States\VacationRequest\WaitingForAdministrative;
use Toby\States\VacationRequest\WaitingForTechnical;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionsSeeder::class);

        $user = User::factory([
            "email" => config("auth.local_email_for_login_via_google"),
            "remember_token" => Str::random(10),
        ])
            ->admin()
            ->hasProfile([
                "first_name" => "Jan",
                "last_name" => "Kowalski",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "programista",
            ])
            ->create();
        $user->histories()->create([
            "from" => Carbon::createFromDate(2021, 12, 31),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::EmploymentContract,
            "is_employed_at_current_company" => true,
        ]);

        $programmer = User::factory([
            "email" => "jerzy.nowak@example.com",
            "remember_token" => Str::random(10),
        ])
            ->employee()
            ->hasProfile([
                "first_name" => "Jerzy",
                "last_name" => "Nowak",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "programista",
            ])
            ->create();
        $programmer->histories()->create([
            "from" => Carbon::createFromDate(2021, 5, 10),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::EmploymentContract,
            "is_employed_at_current_company" => true,
        ]);

        $tester = User::factory([
            "email" => "anna.nowak@example.com",
            "remember_token" => Str::random(10),
        ])
            ->employee()
            ->hasProfile([
                "first_name" => "Anna",
                "last_name" => "Nowak",
                "employment_form" => EmploymentForm::CommissionContract,
                "position" => "tester",
            ])
            ->create();
        $tester->histories()->create([
            "from" => Carbon::createFromDate(2021, 5, 10),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::CommissionContract,
            "is_employed_at_current_company" => true,
        ]);

        $programmer = User::factory([
            "email" => "tola.sawicka@example.com",
            "role" => Role::Employee,
            "remember_token" => Str::random(10),
        ])
            ->employee()
            ->hasProfile([
                "first_name" => "Tola",
                "last_name" => "Sawicka",
                "employment_form" => EmploymentForm::B2bContract,
                "position" => "programista",
            ])
            ->create();
        $programmer->histories()->create([
            "from" => Carbon::createFromDate(2021, 1, 4),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::B2bContract,
            "is_employed_at_current_company" => true,
        ]);

        $technicalApprover = User::factory([
            "email" => "maciej.ziolkowski@example.com",
            "remember_token" => Str::random(10),
        ])
            ->technicalApprover()
            ->hasProfile([
                "first_name" => "Maciej",
                "last_name" => "Ziółkowski",
                "employment_form" => EmploymentForm::BoardMemberContract,
                "position" => "programista",
            ])
            ->create();
        $technicalApprover->histories()->create([
            "from" => Carbon::createFromDate(2021, 1, 4),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::BoardMemberContract,
            "is_employed_at_current_company" => true,
        ]);

        $administrativeApprover = User::factory([
            "email" => "katarzyna.zajac@example.com",
            "remember_token" => Str::random(10),
        ])
            ->administrativeApprover()
            ->hasProfile([
                "first_name" => "Katarzyna",
                "last_name" => "Zając",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "dyrektor",
            ])
            ->create();
        $administrativeApprover->histories()->create([
            "from" => Carbon::createFromDate(2021, 1, 4),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::EmploymentContract,
            "is_employed_at_current_company" => true,
        ]);

        $administrator = User::factory([
            "email" => "milosz.borowski@example.com",
            "remember_token" => Str::random(10),
        ])
            ->admin()
            ->hasProfile([
                "first_name" => "Miłosz",
                "last_name" => "Borowski",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "administrator",
            ])
            ->create();
        $administrator->histories()->create([
            "from" => Carbon::createFromDate(2021, 1, 4),
            "to" => null,
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::EmploymentContract,
            "is_employed_at_current_company" => true,
        ]);

        $users = User::all();

        $year = Carbon::now()->year;

        foreach ([$year - 1, $year, $year + 1] as $i) {
            $polishHolidaysRetriever = new PolishHolidaysRetriever();

            foreach ($polishHolidaysRetriever->getForYear($i) as $holiday) {
                Holiday::query()->create([
                    "name" => $holiday["name"],
                    "date" => $holiday["date"],
                ]);
            }
        }

        foreach ($users as $item) {
            VacationLimit::factory()
                ->for($item)
                ->create(["year" => $year]);
        }

        /** @var VacationRequest $vacationRequestApproved */
        $vacationRequestApproved = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Created::class,
            "from" => Carbon::create($year, 1, 31)->toDateString(),
            "to" => Carbon::create($year, 2, 4)->toDateString(),
            "comment" => "Komentarz do wniosku urlopowego.",
        ])
            ->for($user)
            ->for($user, "creator")
            ->afterCreating(function (VacationRequest $vacationRequest): void {
                $days = app(WorkDaysCalculator::class)->calculateDays(
                    $vacationRequest->from,
                    $vacationRequest->to,
                    $vacationRequest->type,
                );

                foreach ($days as $day) {
                    $vacationRequest->vacations()->create([
                        "date" => $day,
                        "user_id" => $vacationRequest->user->id,
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
            "from" => Carbon::create($year, 2, 14)->toDateString(),
            "to" => Carbon::create($year, 2, 14)->toDateString(),
            "comment" => "Komentarz do wniosku urlopowego.",
        ])
            ->for($user)
            ->for($user, "creator")
            ->afterCreating(function (VacationRequest $vacationRequest): void {
                $days = app(WorkDaysCalculator::class)->calculateDays(
                    $vacationRequest->from,
                    $vacationRequest->to,
                    $vacationRequest->type,
                );

                foreach ($days as $day) {
                    $vacationRequest->vacations()->create([
                        "date" => $day,
                        "user_id" => $vacationRequest->user->id,
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
            "from" => Carbon::create($year, 2, 7)->toDateString(),
            "to" => Carbon::create($year, 2, 7)->toDateString(),
            "comment" => "",
        ])
            ->for($user)
            ->for($user, "creator")
            ->afterCreating(function (VacationRequest $vacationRequest): void {
                $days = app(WorkDaysCalculator::class)->calculateDays(
                    $vacationRequest->from,
                    $vacationRequest->to,
                    $vacationRequest->type,
                );

                foreach ($days as $day) {
                    $vacationRequest->vacations()->create([
                        "date" => $day,
                        "user_id" => $vacationRequest->user->id,
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

        EquipmentLabel::factory()->createMany([
            ["name" => "Komputery"],
            ["name" => "Telefony"],
            ["name" => "Urządzenia peryferyjne"],
            ["name" => "Monitory"],
            ["name" => "Telewizory"],
            ["name" => "Wyposażenie biura"],
            ["name" => "Inne"],
        ]);

        User::query()->each(function (User $user): void {
            /** @var EquipmentItem $computer */
            $computer = EquipmentItem::factory([
                "name" => "Laptop Dell Latitude 7400",
                "assigned_at" => fake()->dateTimeBetween("-1 year"),
                "is_mobile" => true,
                "labels" => [
                    "Komputery",
                ],
            ])->for($user, "assignee")->create();

            EquipmentItem::factory([
                "name" => "Monitor Philips 2" . fake()->numberBetween(1, 8) . '"',
                "assigned_at" => $computer->assigned_at,
                "is_mobile" => false,
                "labels" => [
                    "Monitory",
                    "Urządzenia peryferyjne",
                ],
            ])->for($user, "assignee")->create();

            EquipmentItem::factory([
                "name" => "Myszka Logitech MX" . fake()->numerify(),
                "assigned_at" => $computer->assigned_at,
                "is_mobile" => true,
                "labels" => [
                    "Urządzenia peryferyjne",
                ],
            ])->for($user, "assignee")->create();

            EquipmentItem::factory([
                "name" => "Klawiatura Dell " . fake()->numerify("#####"),
                "assigned_at" => $computer->assigned_at,
                "is_mobile" => true,
                "labels" => [
                    "Urządzenia peryferyjne",
                ],
            ])->for($user, "assignee")->create();

            EquipmentItem::factory([
                "name" => "Hub USB",
                "assigned_at" => $computer->assigned_at,
                "is_mobile" => true,
                "labels" => [
                    "Urządzenia peryferyjne",
                ],
            ])->for($user, "assignee")->create();
        });

        EquipmentItem::factory([
            "name" => 'Telewizor TCN 55" 4K',
            "is_mobile" => false,
            "assigned_at" => null,
            "assignee_id" => null,
            "labels" => [
                "Telewizory",
                "Wyposażenie biura",
            ],
        ])->create();
    }
}
