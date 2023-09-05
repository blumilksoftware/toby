<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\BenefitsReportCreationNotification;
use Toby\Eloquent\Models\User;

class SendNotificationAboutBenefitsReportCreation extends Command
{
    protected $signature = "toby:send-notification-about-benefits-report-creation";
    protected $description = "Send notifications about benefits report creation.";

    public function handle(): void
    {
        $usersToNotify = User::query()
            ->whereIn("role", [Role::AdministrativeApprover])
            ->get();

        foreach ($usersToNotify as $user) {
            $user->notify(new BenefitsReportCreationNotification());
        }
    }
}
