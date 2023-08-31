<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Eloquent\Models\User;

class SendNotificationAboutUpcomingAndOverdueMedicalExams extends Command
{
    protected $signature = "toby:send-notification-about-medical-exams";
    protected $description = "Send notifications about upcoming and overdue medical exams.";

    public function handle(): void
    {
        $usersToNotify = User::query()
            ->whereIn("role", [Role::AdministrativeApprover])
            ->get();

        foreach ($usersToNotify as $user)
        {
            $user->notify(new UpcomingAndOverdueMedicalExamsNotification());
        }
    }
}
