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
use Toby\Infrastructure\Console\Commands\SendNotificationAboutUpcomingAndOverdueMedicalExams;

class UpcomingAndOverdueMedicalExamsNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueMedicalExams(): void
    {
        Notification::fake();

        $userWithUpcomingMedicalExam = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_medical_exam_date" => Carbon::now()->addMonth(),
        ])->create();

        $userWithDistantMedicalExamDate = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_medical_exam_date" => Carbon::now()->addYear(),
        ])->create();

        $userWithOverdueMedicalExam = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_medical_exam_date" => Carbon::now()->subMonth(),
        ])->create();

        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
            ->execute();

        Notification::assertSentTo(
            $administrativeApprover,
            UpcomingAndOverdueMedicalExamsNotification::class,
            function ($notification) use ($administrativeApprover, $userWithUpcomingMedicalExam, $userWithOverdueMedicalExam, $userWithDistantMedicalExamDate) {
                $mailData = $notification->toMail($administrativeApprover)->toArray();
                $this->assertContains("{$userWithUpcomingMedicalExam->profile->full_name} - {$userWithUpcomingMedicalExam->profile->next_medical_exam_date->toDisplayString()} (za {$userWithUpcomingMedicalExam->profile->next_medical_exam_date->diffInDays(Carbon::today())} dni)", $mailData["introLines"]);
                $this->assertContains("{$userWithOverdueMedicalExam->profile->full_name} - {$userWithOverdueMedicalExam->profile->next_medical_exam_date->toDisplayString()} (przeterminowane {$userWithOverdueMedicalExam->profile->next_medical_exam_date->diffInDays(Carbon::today())} dni)", $mailData["introLines"]);
                $this->assertNotContains("{$userWithDistantMedicalExamDate->profile->full_name} - {$userWithDistantMedicalExamDate->profile->next_medical_exam_date->toDisplayString()} (za {$userWithDistantMedicalExamDate->profile->next_medical_exam_date->diffInDays(Carbon::today())} dni)", $mailData["introLines"]);

                return true;
            },
        );
        Notification::assertNotSentTo([$userWithUpcomingMedicalExam, $userWithOverdueMedicalExam, $userWithDistantMedicalExamDate], UpcomingAndOverdueMedicalExamsNotification::class);
    }

    public function testNotSendingNotificationAboutUpcomingAndOverdueMedicalExamsWhenNoUsers(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->create();

        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
            ->execute();

        Notification::assertNotSentTo([$administrativeApprover, $user], UpcomingAndOverdueMedicalExamsNotification::class);
    }
}
