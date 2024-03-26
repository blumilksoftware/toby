<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Toby\Domain\Notifications\BenefitsReportCreationNotification;

class SendNotificationAboutBenefitsReportCreation extends Command
{
    protected $signature = "toby:send-notification-about-benefits-report-creation";
    protected $description = "Send notifications about benefits report creation.";

    public function handle(): void
    {
        $usersToNotify = Permission::findByName("receiveBenefitsReportCreationNotification")->users()->get();

        foreach ($usersToNotify as $user) {
            $user->notify(new BenefitsReportCreationNotification());
        }
    }
}
