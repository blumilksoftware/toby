<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Console\Commands\SendNotificationAboutUpcomingMedicalExamsForEmployees;
use Toby\Models\User;
use Toby\Notifications\UpcomingMedicalExamForEmployeeNotification;

class SendNotificationAboutUpcomingMedicalExamsToSlackTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->user = User::factory()->employee()->create();
    }

    public function testNotificationIsSentToUserWithUpcomingMedicalExams(): void
    {
        $this->user->histories()->create([
            "from" => Carbon::createFromDate(2022, 1, 1),
            "to" => Carbon::now()->addDays(14),
            "type" => "medical_exam",
        ]);

        $this->artisan(SendNotificationAboutUpcomingMedicalExamsForEmployees::class)
            ->execute();

        Notification::assertSentTo($this->user, UpcomingMedicalExamForEmployeeNotification::class);
    }

    public function testNotificationIsNotSentToUserWithoutUpcomingMedicalExams(): void
    {
        $this->user->histories()->create([
            "from" => Carbon::createFromDate(2022, 1, 1),
            "to" => Carbon::now()->addYear(),
            "type" => "medical_exam",
        ]);

        $this->artisan(SendNotificationAboutUpcomingMedicalExamsForEmployees::class)
            ->execute();

        Notification::assertNothingSent();
    }
}
