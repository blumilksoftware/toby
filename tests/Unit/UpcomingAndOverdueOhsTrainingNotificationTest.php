<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\UpcomingAndOverdueOhsTrainingNotification;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Console\Commands\SendNotificationAboutUpcomingAndOverdueOhsTraining;

class UpcomingAndOverdueOhsTrainingNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutUpcomingAndOverdueOhsTraining(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->hasProfile([
            "next_ohs_training_date" => Carbon::now()->addMonth(),
        ])->create();

        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $this->artisan(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
            ->execute();

        Notification::assertSentTo($administrativeApprover, UpcomingAndOverdueOhsTrainingNotification::class);
        Notification::assertNotSentTo($user, UpcomingAndOverdueOhsTrainingNotification::class);
    }
}
