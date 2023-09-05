<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\BenefitsReportCreationNotification;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Console\Commands\SendNotificationAboutBenefitsReportCreation;

class BenefitsReportCreationNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutBenefitsReportCreation(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->create();
        $technicalApprover = User::factory([
            "role" => Role::TechnicalApprover,
        ])->create();
        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();

        $admin = User::factory([
            "role" => Role::Administrator,
        ])->create();

        $this->artisan(SendNotificationAboutBenefitsReportCreation::class)
            ->execute();

        Notification::assertSentTo($administrativeApprover, BenefitsReportCreationNotification::class);
        Notification::assertNotSentTo([$user, $technicalApprover, $admin], BenefitsReportCreationNotification::class);
    }
}
