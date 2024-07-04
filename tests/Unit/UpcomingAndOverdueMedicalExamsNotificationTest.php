<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Console\Commands\SendNotificationAboutUpcomingAndOverdueMedicalExams;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Notifications\UpcomingAndOverdueMedicalExamsNotification;

class UpcomingAndOverdueMedicalExamsNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueMedicalExams(): void
    {
        Notification::fake();

        $userWithUpcomingMedicalExam = User::factory()->employee()->hasProfile()->create();
        $userWithUpcomingMedicalExam->histories()->create([
            "type" => UserHistoryType::MedicalExam,
            "from" => Carbon::now()->subMonth(),
            "to" => Carbon::now()->addMonth(),
        ]);

        $userWithDistantMedicalExamDate = User::factory()->employee()->hasProfile()->create();
        $userWithDistantMedicalExamDate->histories()->create([
            "type" => UserHistoryType::MedicalExam,
            "from" => Carbon::now()->subMonth(),
            "to" => Carbon::now()->addYear(),
        ]);

        $userWithOverdueMedicalExam = User::factory()->employee()->hasProfile()->create();
        $userWithOverdueMedicalExam->histories()->create([
            "type" => UserHistoryType::MedicalExam,
            "from" => Carbon::now()->subMonth(),
            "to" => Carbon::now()->subMonth(),
        ]);

        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
            ->execute();

        Notification::assertSentTo(
            $administrativeApprover,
            UpcomingAndOverdueMedicalExamsNotification::class,
            function ($notification) use ($administrativeApprover, $userWithUpcomingMedicalExam, $userWithOverdueMedicalExam, $userWithDistantMedicalExamDate) {
                $mailData = $notification->toMail($administrativeApprover)->toArray();

                $lastMedicalExamDate = $userWithUpcomingMedicalExam->lastMedicalExam();
                $this->assertContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithUpcomingMedicalExam->profile->full_name,
                    "date" => $lastMedicalExamDate->to->toDisplayString(),
                    "difference" => (int)$lastMedicalExamDate->to->diffInDays(Carbon::today(), true),
                ]), $mailData["introLines"]);

                $lastMedicalExamDate = $userWithOverdueMedicalExam->lastMedicalExam();
                $this->assertContains(__(":user - :date (overdue :difference days)", [
                    "user" => $userWithOverdueMedicalExam->profile->full_name,
                    "date" => $lastMedicalExamDate->to->toDisplayString(),
                    "difference" => (int)$lastMedicalExamDate->to->diffInDays(Carbon::today(), true),
                ]), $mailData["introLines"]);

                $lastMedicalExamDate = $userWithOverdueMedicalExam->lastMedicalExam();
                $this->assertNotContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithDistantMedicalExamDate->profile->full_name,
                    "date" => $lastMedicalExamDate->to->toDisplayString(),
                    "difference" => (int)$lastMedicalExamDate->to->diffInDays(Carbon::today(), true),
                ]), $mailData["introLines"]);

                return true;
            },
        );
        Notification::assertNotSentTo([$userWithUpcomingMedicalExam, $userWithOverdueMedicalExam, $userWithDistantMedicalExamDate], UpcomingAndOverdueMedicalExamsNotification::class);
    }

    public function testNotSendingNotificationAboutUpcomingAndOverdueMedicalExamsWhenNoUsers(): void
    {
        Notification::fake();

        $user = User::factory()->employee()->create();

        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
            ->execute();

        Notification::assertNotSentTo([$administrativeApprover, $user], UpcomingAndOverdueMedicalExamsNotification::class);
    }
}
