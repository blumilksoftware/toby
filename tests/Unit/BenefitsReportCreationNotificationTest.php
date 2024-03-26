<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Console\Commands\SendNotificationAboutBenefitsReportCreation;
use Toby\Domain\Notifications\BenefitsReportCreationNotification;
use Toby\Models\User;

class BenefitsReportCreationNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendingNotificationAboutBenefitsReportCreation(): void
    {
        Notification::fake();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();
        $admin = User::factory()->admin()->create();

        $this->artisan(SendNotificationAboutBenefitsReportCreation::class)
            ->execute();

        Notification::assertSentTo($administrativeApprover, BenefitsReportCreationNotification::class);
        Notification::assertNotSentTo([$user, $technicalApprover, $admin], BenefitsReportCreationNotification::class);
    }
}
