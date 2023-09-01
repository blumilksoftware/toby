<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Domain\Notifications\UpcomingAndOverdueOhsTrainingNotification;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Console\Commands\SendNotificationAboutUpcomingAndOverdueOhsTraining;

class UpcomingAndOverdueOhsTrainingNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueOhsTraining(): void
    {
        Notification::fake();

        $userWithUpcomingOhsTraining = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_ohs_training_date" => Carbon::now()->addMonth(),
        ])->create();

        $userWithDistantOhsTrainingDate = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_ohs_training_date" => Carbon::now()->addYear(),
        ])->create();

        $userWithOverdueOhsTraining = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_ohs_training_date" => Carbon::now()->subMonth(),
        ])->create();

        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
            ->execute();

        Notification::assertSentTo(
            $administrativeApprover,
            UpcomingAndOverdueOhsTrainingNotification::class,
            function ($notification) use ($administrativeApprover, $userWithUpcomingOhsTraining, $userWithDistantOhsTrainingDate, $userWithOverdueOhsTraining) {
                $mailData = $notification->toMail($administrativeApprover)->toArray();
                $this->assertContains("{$userWithUpcomingOhsTraining->profile->full_name} - {$userWithUpcomingOhsTraining->profile->next_ohs_training_date->toDisplayString()} (za {$userWithUpcomingOhsTraining->profile->next_ohs_training_date->diffInDays(Carbon::today())} dni)", $mailData["introLines"]);
                $this->assertContains("{$userWithOverdueOhsTraining->profile->full_name} - {$userWithOverdueOhsTraining->profile->next_ohs_training_date->toDisplayString()} (przeterminowane {$userWithOverdueOhsTraining->profile->next_ohs_training_date->diffInDays(Carbon::today())} dni)", $mailData["introLines"]);
                $this->assertNotContains("{$userWithDistantOhsTrainingDate->profile->full_name} - {$userWithDistantOhsTrainingDate->profile->next_ohs_training_date->toDisplayString()} (za {$userWithDistantOhsTrainingDate->profile->next_ohs_training_date->diffInDays(Carbon::today())} dni)", $mailData["introLines"]);

                return true;
            },
        );
        Notification::assertNotSentTo([$userWithUpcomingOhsTraining, $userWithDistantOhsTrainingDate, $userWithOverdueOhsTraining], UpcomingAndOverdueOhsTrainingNotification::class);
    }

    public function testNotSendingNotificationAboutUpcomingAndOverdueOhsTrainingWhenNoUsers(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->create();

        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
            ->execute();

        Notification::assertNotSentTo([$administrativeApprover, $user], UpcomingAndOverdueMedicalExamsNotification::class);
    }
}
