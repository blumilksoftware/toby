<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Domain\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Console\Commands\SendNotificationAboutUpcomingAndOverdueMedicalExams;

class UpcomingAndOverdueMedicalExamsNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueMedicalExams(): void
    {
        Notification::fake();

        $userWithUpcomingMedicalExam = User::factory()->employee()->hasProfile([
            "next_medical_exam_date" => Carbon::now()->addMonth(),
        ])->create();

        $userWithDistantMedicalExamDate = User::factory()->employee()->hasProfile([
            "next_medical_exam_date" => Carbon::now()->addYear(),
        ])->create();

        $userWithOverdueMedicalExam = User::factory()->employee()->hasProfile([
            "next_medical_exam_date" => Carbon::now()->subMonth(),
        ])->create();

        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
            ->execute();

        Notification::assertSentTo(
            $administrativeApprover,
            UpcomingAndOverdueMedicalExamsNotification::class,
            function ($notification) use ($administrativeApprover, $userWithUpcomingMedicalExam, $userWithOverdueMedicalExam, $userWithDistantMedicalExamDate) {
                $mailData = $notification->toMail($administrativeApprover)->toArray();

                $this->assertContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithUpcomingMedicalExam->profile->full_name,
                    "date" => $userWithUpcomingMedicalExam->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => $userWithUpcomingMedicalExam->profile->next_medical_exam_date->diffInDays(Carbon::today()),
                ]), $mailData["introLines"]);

                $this->assertContains(__(":user - :date (overdue :difference days)", [
                    "user" => $userWithOverdueMedicalExam->profile->full_name,
                    "date" => $userWithOverdueMedicalExam->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => $userWithOverdueMedicalExam->profile->next_medical_exam_date->diffInDays(Carbon::today()),
                ]), $mailData["introLines"]);

                $this->assertNotContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithDistantMedicalExamDate->profile->full_name,
                    "date" => $userWithDistantMedicalExamDate->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => $userWithDistantMedicalExamDate->profile->next_medical_exam_date->diffInDays(Carbon::today()),
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
