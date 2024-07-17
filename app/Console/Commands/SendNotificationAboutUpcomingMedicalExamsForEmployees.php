<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Notifications\UpcomingMedicalExamForEmployeeNotification;

class SendNotificationAboutUpcomingMedicalExamsForEmployees extends Command
{
    protected $signature = "toby:send-notification-about-medical-exams-for-employees";
    protected $description = "Send notifications about upcoming medical exams.";

    public function handle(): void
    {
        $usersUpcomingMedicalExams = User::query()
            ->whereRelation("histories", function ($query): void {
                $query->where("type", UserHistoryType::MedicalExam)
                    ->where("to", ">", Carbon::now())
                    ->where("to", "<=", Carbon::now()->addMonth());
            })->get();

        if ($usersUpcomingMedicalExams->isEmpty()) {
            return;
        }

        foreach ($usersUpcomingMedicalExams as $user) {
            $user->notify(new UpcomingMedicalExamForEmployeeNotification($user));
        }
    }
}
