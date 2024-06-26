<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Notifications\UpcomingAndOverdueMedicalExamsNotification;

class SendNotificationAboutUpcomingAndOverdueMedicalExams extends Command
{
    protected $signature = "toby:send-notification-about-medical-exams";
    protected $description = "Send notifications about upcoming and overdue medical exams.";

    public function handle(): void
    {
        $usersToNotify = Permission::findByName("receiveUpcomingAndOverdueMedicalExamsNotification")->users()->get();

        $usersUpcomingMedicalExams = User::query()
            ->whereRelation("histories", function ($query): void {
                $query->where("type", UserHistoryType::MedicalExam)
                    ->where("to", ">", Carbon::now())
                    ->where("to", "<=", Carbon::now()->addMonths(2));
            })->get();

        $usersOverdueMedicalExams = User::query()
            ->whereDoesntHave("histories", function ($query): void {
                $query->where("type", UserHistoryType::MedicalExam)
                    ->where("to", ">", Carbon::now());
            })->withWhereHas("histories", function ($query): void {
                $query->where("type", UserHistoryType::MedicalExam)
                    ->where("to", "<", Carbon::now());
            })->get();

        if ($usersUpcomingMedicalExams->isEmpty() && $usersOverdueMedicalExams->isEmpty()) {
            return;
        }

        foreach ($usersToNotify as $user) {
            $user->notify(new UpcomingAndOverdueMedicalExamsNotification($usersUpcomingMedicalExams, $usersOverdueMedicalExams));
        }
    }
}
