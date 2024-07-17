<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Console\Commands\SendNotificationAboutUpcomingOhsTrainingsForEmployees;
use Toby\Models\User;
use Toby\Notifications\UpcomingOhsTrainingForEmployeeNotification;

class SendNotificationAboutUpcomingOhsTrainingsToSlackTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithYearPeriods;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->createCurrentYearPeriod();
        Http::fake(fn(): array => [
            "channel" => Str::random(8),
            "message" => ["ts" => Carbon::now()->toDateTimeString()],
        ]);

        $this->user = User::factory()->employee()->create();
    }

    public function testNotificationIsSentToUserWithUpcomingMedicalExams(): void
    {
        $this->user->histories()->create([
            "from" => Carbon::createFromDate(2022, 1, 1),
            "to" => Carbon::now()->addDays(14),
            "type" => "ohs_training",
        ]);

        $this->artisan(SendNotificationAboutUpcomingOhsTrainingsForEmployees::class)
            ->execute();

        Notification::assertSentTo($this->user, UpcomingOhsTrainingForEmployeeNotification::class);
    }

    public function testNotificationIsNotSentToUserWithoutUpcomingMedicalExams(): void
    {
        $this->user->histories()->create([
            "from" => Carbon::createFromDate(2022, 1, 1),
            "to" => Carbon::now()->addYear(),
            "type" => "ohs_training",
        ]);

        $this->artisan(SendNotificationAboutUpcomingOhsTrainingsForEmployees::class)
            ->execute();

        Notification::assertNothingSent();
    }
}
