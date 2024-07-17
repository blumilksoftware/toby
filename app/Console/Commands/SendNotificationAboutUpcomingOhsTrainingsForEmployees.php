<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Notifications\UpcomingOhsTrainingForEmployeeNotification;

class SendNotificationAboutUpcomingOhsTrainingsForEmployees extends Command
{
    protected $signature = "toby:send-notification-about-ohs-trainings-for-employees";
    protected $description = "Send notifications about upcoming ohs trainings.";

    public function handle(): void
    {
        $usersUpcomingOhsTrainings = User::query()
            ->whereRelation("histories", function ($query): void {
                $query->where("type", UserHistoryType::OhsTraining)
                    ->where("to", ">", Carbon::now())
                    ->where("to", "<=", Carbon::now()->addMonth());
            })->get();

        if ($usersUpcomingOhsTrainings->isEmpty()) {
            return;
        }

        foreach ($usersUpcomingOhsTrainings as $user) {
            $user->notify(new UpcomingOhsTrainingForEmployeeNotification($user));
        }
    }
}
