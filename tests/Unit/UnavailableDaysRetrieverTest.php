<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\UnavailableDaysRetriever;
use Toby\Enums\Role;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\Vacation;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;
use Toby\States\VacationRequest\Approved;

class UnavailableDaysRetrieverTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithSession;
    use InteractsWithYearPeriods;

    public UnavailableDaysRetriever $unavailableDaysRetriever;
    public User $user;
    public YearPeriod $yearPeriod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unavailableDaysRetriever = $this->app->make(UnavailableDaysRetriever::class);
        $this->user = User::factory(["role" => Role::Employee])->create();
        $this->yearPeriod = $this->createYearPeriod(2023);

        $vacationRequest = VacationRequest::factory()
            ->for($this->user)
            ->for($this->yearPeriod)
            ->create([
                "from" => Carbon::parse("2023-07-05"),
                "to" => Carbon::parse("2023-07-09"),
                "state" => Approved::class,
            ]);

        Vacation::factory()
            ->for($vacationRequest)
            ->for($this->yearPeriod)
            ->for($this->user)
            ->count(5)
            ->state(new Sequence(
                ["date" => Carbon::parse("2023-07-05")],
                ["date" => Carbon::parse("2023-07-06")],
                ["date" => Carbon::parse("2023-07-07")],
                ["date" => Carbon::parse("2023-07-08")],
                ["date" => Carbon::parse("2023-07-09")],
            ))
            ->create();

        $polishHolidaysRetriever = $this->app->make(PolishHolidaysRetriever::class);

        foreach ($polishHolidaysRetriever->getForYearPeriod($this->yearPeriod) as $holiday) {
            $this->yearPeriod->holidays()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }
    }

    public function testRetrievesCorrectUnavailableDays(): void
    {
        $expectedUnavailableDays = [
            "2023-01-01",
            "2023-01-06",
            "2023-01-07",
            "2023-01-08",
            "2023-01-14",
            "2023-01-15",
            "2023-01-21",
            "2023-01-22",
            "2023-01-28",
            "2023-01-29",
            "2023-02-04",
            "2023-02-05",
            "2023-02-11",
            "2023-02-12",
            "2023-02-18",
            "2023-02-19",
            "2023-02-25",
            "2023-02-26",
            "2023-03-04",
            "2023-03-05",
            "2023-03-11",
            "2023-03-12",
            "2023-03-18",
            "2023-03-19",
            "2023-03-25",
            "2023-03-26",
            "2023-04-01",
            "2023-04-02",
            "2023-04-08",
            "2023-04-09",
            "2023-04-10",
            "2023-04-15",
            "2023-04-16",
            "2023-04-22",
            "2023-04-23",
            "2023-04-29",
            "2023-04-30",
            "2023-05-01",
            "2023-05-03",
            "2023-05-06",
            "2023-05-07",
            "2023-05-13",
            "2023-05-14",
            "2023-05-20",
            "2023-05-21",
            "2023-05-27",
            "2023-05-28",
            "2023-06-03",
            "2023-06-04",
            "2023-06-08",
            "2023-06-10",
            "2023-06-11",
            "2023-06-17",
            "2023-06-18",
            "2023-06-24",
            "2023-06-25",
            "2023-07-01",
            "2023-07-02",
            "2023-07-05",
            "2023-07-06",
            "2023-07-07",
            "2023-07-08",
            "2023-07-09",
            "2023-07-15",
            "2023-07-16",
            "2023-07-22",
            "2023-07-23",
            "2023-07-29",
            "2023-07-30",
            "2023-08-05",
            "2023-08-06",
            "2023-08-12",
            "2023-08-13",
            "2023-08-15",
            "2023-08-19",
            "2023-08-20",
            "2023-08-26",
            "2023-08-27",
            "2023-09-02",
            "2023-09-03",
            "2023-09-09",
            "2023-09-10",
            "2023-09-16",
            "2023-09-17",
            "2023-09-23",
            "2023-09-24",
            "2023-09-30",
            "2023-10-01",
            "2023-10-07",
            "2023-10-08",
            "2023-10-14",
            "2023-10-15",
            "2023-10-21",
            "2023-10-22",
            "2023-10-28",
            "2023-10-29",
            "2023-11-01",
            "2023-11-04",
            "2023-11-05",
            "2023-11-11",
            "2023-11-12",
            "2023-11-18",
            "2023-11-19",
            "2023-11-25",
            "2023-11-26",
            "2023-12-02",
            "2023-12-03",
            "2023-12-09",
            "2023-12-10",
            "2023-12-16",
            "2023-12-17",
            "2023-12-23",
            "2023-12-24",
            "2023-12-25",
            "2023-12-26",
            "2023-12-30",
            "2023-12-31",
        ];

        $actualUnavailableDays = $this->unavailableDaysRetriever->getUnavailableDays($this->user, $this->yearPeriod)
            ->map(fn(Carbon $date): string => $date->toDateString())
            ->toArray();

        $this->assertSame($expectedUnavailableDays, $actualUnavailableDays);
    }

    public function testRetrievesCorrectUnavailableDaysForNonWorkDaysVacation(): void
    {
        $expectedUnavailableDays = [
            "2023-07-05",
            "2023-07-06",
            "2023-07-07",
            "2023-07-08",
            "2023-07-09",
        ];

        $actualUnavailableDays = $this->unavailableDaysRetriever
            ->getUnavailableDays($this->user, $this->yearPeriod, VacationType::Delegation)
            ->map(fn(Carbon $date): string => $date->toDateString())
            ->toArray();

        $this->assertSame($expectedUnavailableDays, $actualUnavailableDays);
    }
}
