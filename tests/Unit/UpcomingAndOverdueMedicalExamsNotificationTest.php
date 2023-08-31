<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Console\Commands\SendNotificationAboutUpcomingAndOverdueMedicalExams;

class UpcomingAndOverdueMedicalExamsNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueMedicalExams(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_medical_exam_date" => Carbon::now()->addMonth(),
        ])->create();

        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $currentYearPeriod = YearPeriod::current();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
            ->execute();

        Notification::assertSentTo($administrativeApprover, UpcomingAndOverdueMedicalExamsNotification::class);
        Notification::assertNotSentTo($user, UpcomingAndOverdueMedicalExamsNotification::class);
    }
}
