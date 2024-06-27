<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Toby\Enums\UserHistoryType;
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
            ->whereRelation("histories", function ($query): void {
                $query->where("type", UserHistoryType::OhsTraining)
                    ->where("to", ">", Carbon::now())
                    ->where("to", "<=", Carbon::now()->addMonths(2));
            })->get();

        $usersForOverdueOhsTraining = User::query()
            ->whereRelation("histories", function ($query): void {
                $query->where("type", UserHistoryType::OhsTraining)
                    ->where("to", "<", Carbon::now());
            })->get();

        if ($usersForUpcomingOhsTraining->isEmpty() && $usersForOverdueOhsTraining->isEmpty()) {
            return;
        }

        foreach ($usersToNotify as $user) {
            $user->notify(new UpcomingAndOverdueOhsTrainingNotification($usersForUpcomingOhsTraining, $usersForOverdueOhsTraining));
        }
    }
}
