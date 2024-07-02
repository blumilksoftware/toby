<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Console\Commands\SendNotificationAboutUpcomingAndOverdueOhsTraining;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Notifications\UpcomingAndOverdueOhsTrainingNotification;

class UpcomingAndOverdueOhsTrainingNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueOhsTraining(): void
    {
        Notification::fake();

        $userWithUpcomingOhsTraining = User::factory()->employee()->hasProfile()->create();
        $userWithUpcomingOhsTraining->histories()->create([
            "type" => UserHistoryType::OhsTraining,
            "from" => Carbon::now()->subMonth(),
            "to" => Carbon::now()->addMonth(),
        ]);

        $userWithDistantOhsTrainingDate = User::factory()->employee()->hasProfile()->create();
        $userWithDistantOhsTrainingDate->histories()->create([
            "type" => UserHistoryType::OhsTraining,
            "from" => Carbon::now()->subMonth(),
            "to" => Carbon::now()->addYear(),
        ]);

        $userWithOverdueOhsTraining = User::factory()->employee()->hasProfile()->create();
        $userWithOverdueOhsTraining->histories()->create([
            "type" => UserHistoryType::OhsTraining,
            "from" => Carbon::now()->subMonth(),
            "to" => Carbon::now()->subMonth(),
        ]);

        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
            ->execute();

        Notification::assertSentTo(
            $administrativeApprover,
            UpcomingAndOverdueOhsTrainingNotification::class,
            function ($notification) use ($administrativeApprover, $userWithUpcomingOhsTraining, $userWithDistantOhsTrainingDate, $userWithOverdueOhsTraining) {
                $mailData = $notification->toMail($administrativeApprover)->toArray();

                $lastOhsTraining = $userWithUpcomingOhsTraining->lastOhsTraining();
                $this->assertContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithUpcomingOhsTraining->profile->full_name,
                    "date" => $lastOhsTraining->to->toDisplayString(),
                    "difference" => (int)$lastOhsTraining->to->diffInDays(Carbon::today(), true),
                ]), $mailData["introLines"]);

                $lastOhsTraining = $userWithOverdueOhsTraining->lastOhsTraining();
                $this->assertContains(__(":user - :date (overdue :difference days)", [
                    "user" => $userWithOverdueOhsTraining->profile->full_name,
                    "date" => $lastOhsTraining->to->toDisplayString(),
                    "difference" => (int)$lastOhsTraining->to->diffInDays(Carbon::today(), true),
                ]), $mailData["introLines"]);

                $lastOhsTraining = $userWithDistantOhsTrainingDate->lastOhsTraining();
                $this->assertNotContains(__(":user - :date (in :difference days)", [
                    "user" => $userWithDistantOhsTrainingDate->profile->full_name,
                    "date" => $lastOhsTraining->to->toDisplayString(),
                    "difference" => (int)$lastOhsTraining->to->diffInDays(Carbon::today(), true),
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
