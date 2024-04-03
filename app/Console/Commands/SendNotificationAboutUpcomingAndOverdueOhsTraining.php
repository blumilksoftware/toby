<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Toby\Models\User;
use Toby\Notifications\UpcomingAndOverdueOhsTrainingNotification;

class SendNotificationAboutUpcomingAndOverdueOhsTraining extends Command
{
    protected $signature = "toby:send-notification-about-ohs-training";
    protected $description = "Send notifications about upcoming and overdue OHS training.";

    public function handle(): void
    {
        $usersToNotify = Permission::findByName("receiveUpcomingAndOverdueOhsTrainingNotification")->users()->get();

        $usersForUpcomingOhsTraining = User::query()
            ->whereRelation("profile", "next_ohs_training_date", ">", Carbon::now())
            ->whereRelation("profile", "next_ohs_training_date", "<=", Carbon::now()->addMonths(2))
            ->orderByProfileField("next_ohs_training_date", "desc")
            ->get();

        $usersForOverdueOhsTraining = User::query()
            ->whereRelation("profile", "next_ohs_training_date", "<=", Carbon::now())
            ->orderByProfileField("next_ohs_training_date", "desc")
            ->get();

        if ($usersForUpcomingOhsTraining->isEmpty() && $usersForOverdueOhsTraining->isEmpty()) {
            return;
        }

        foreach ($usersToNotify as $user) {
            $user->notify(new UpcomingAndOverdueOhsTrainingNotification($usersForUpcomingOhsTraining, $usersForOverdueOhsTraining));
        }
    }
}
