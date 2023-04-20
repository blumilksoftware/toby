<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Eloquent\Models\Holiday;
use Toby\Infrastructure\Console\Commands\SendDailySummaryToSlack;

class SendDailySummaryToSlackTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(fn(): array => [
            "channel" => Str::random(8),
            "message" => ["ts" => Carbon::now()->toDateTimeString()],
        ]);
        $this->createCurrentYearPeriod();
    }

    public function testCommandSendsMessageToSlackIfWeekday(): void
    {
        $weekDay = Carbon::create(2022, 4, 22);
        $this->assertTrue($weekDay->isWeekday());

        $this->travelTo($weekDay);

        $this->artisan(SendDailySummaryToSlack::class)
            ->execute();

        Http::assertSentCount(1);
    }

    public function testCommandDoesntSendMessageIfWeekend(): void
    {
        $weekend = Carbon::create(2022, 4, 23);
        $this->assertTrue($weekend->isWeekend());

        $this->travelTo($weekend);

        $this->artisan(SendDailySummaryToSlack::class)
            ->execute();

        Http::assertNothingSent();
    }

    public function testCommandDoesntSendMessageIfHoliday(): void
    {
        $holiday = Holiday::factory(["date" => Carbon::create(2022, 4, 22)])->create();

        $this->assertDatabaseHas("holidays", [
            "date" => $holiday->date->toDateString(),
        ]);

        $this->travelTo(Carbon::create(2022, 4, 22));

        $this->artisan(SendDailySummaryToSlack::class)
            ->execute();

        Http::assertNothingSent();
    }

    public function testCommandForceSendsMessageEvenIsWeekendOrHoliday(): void
    {
        $weekend = Carbon::create(2022, 4, 23);
        $this->assertTrue($weekend->isWeekend());

        $this->travelTo($weekend);

        $this->artisan(SendDailySummaryToSlack::class, ["--force" => true])
            ->execute();

        Http::assertSentCount(1);
    }
}
