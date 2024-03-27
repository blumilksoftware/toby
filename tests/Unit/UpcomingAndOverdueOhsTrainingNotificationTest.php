<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Console\Commands\SendNotificationAboutUpcomingAndOverdueOhsTraining;
use Toby\Models\User;
use Toby\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Notifications\UpcomingAndOverdueOhsTrainingNotification;

class UpcomingAndOverdueOhsTrainingNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueOhsTraining(): void
    {
        Notification::fake();

        $userWithUpcomingOhsTraining = User::factory()->employee()->hasProfile([
            "next_ohs_training_date" => Carbon::now()->addMonth(),
        ])->create();

        $userWithDistantOhsTrainingDate = User::factory()->employee()->hasProfile([
            "next_ohs_training_date" => Carbon::now()->addYear(),
        ])->create();

        $userWithOverdueOhsTraining = User::factory()->employee()->hasProfile([
            "next_ohs_training_date" => Carbon::now()->subMonth(),
        ])->create();

        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
            ->execute();

        Notification::assertSentTo(
            $administrativeApprover,
            UpcomingAndOverdueOhsTrainingNotification::class,
            function ($notification) use ($administrativeApprover, $userWithUpcomingOhsTraining, $userWithDistantOhsTrainingDate, $userWithOverdueOhsTraining) {
                $mailData = $notification->toMail($administrativeApprover)->toArray();

                $this->assertContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithUpcomingOhsTraining->profile->full_name,
                    "date" => $userWithUpcomingOhsTraining->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => (int)$userWithUpcomingOhsTraining->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]), $mailData["introLines"]);

                $this->assertContains(__(":user - :date (overdue :difference days)", [
                    "user" => $userWithOverdueOhsTraining->profile->full_name,
                    "date" => $userWithOverdueOhsTraining->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => (int)$userWithOverdueOhsTraining->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]), $mailData["introLines"]);

                $this->assertNotContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithDistantOhsTrainingDate->profile->full_name,
                    "date" => $userWithDistantOhsTrainingDate->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => (int)$userWithDistantOhsTrainingDate->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]), $mailData["introLines"]);

                return true;
            },
        );
        Notification::assertNotSentTo([$userWithUpcomingOhsTraining, $userWithDistantOhsTrainingDate, $userWithOverdueOhsTraining], UpcomingAndOverdueOhsTrainingNotification::class);
    }

    public function testNotSendingNotificationAboutUpcomingAndOverdueOhsTrainingWhenNoUsers(): void
    {
        Notification::fake();

        $user = User::factory()->employee()->create();

        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
            ->execute();

        Notification::assertNotSentTo([$administrativeApprover, $user], UpcomingAndOverdueMedicalExamsNotification::class);
    }
}
