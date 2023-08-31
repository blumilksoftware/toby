<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\UpcomingAndOverdueOhsTrainingNotification;
use Toby\Eloquent\Models\User;

class SendNotificationAboutUpcomingAndOverdueOhsTraining extends Command
{
    protected $signature = "toby:send-notification-about-ohs-training";
    protected $description = "Send notifications about upcoming and overdue OHS training.";

    public function handle(): void
    {
        $usersToNotify = User::query()
            ->whereIn("role", [Role::AdministrativeApprover])
            ->get();

        foreach ($usersToNotify as $user)
        {
            $user->notify(new UpcomingAndOverdueOhsTrainingNotification());
        }
    }
}
