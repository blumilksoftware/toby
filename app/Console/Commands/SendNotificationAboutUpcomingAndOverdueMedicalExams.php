<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
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
            ->whereRelation("profile", "next_medical_exam_date", ">", Carbon::now())
            ->whereRelation("profile", "next_medical_exam_date", "<=", Carbon::now()->addMonths(2))
            ->orderByProfileField("next_medical_exam_date", "desc")
            ->get();

        $usersOverdueMedicalExams = User::query()
            ->whereRelation("profile", "next_medical_exam_date", "<=", Carbon::now())
            ->orderByProfileField("next_medical_exam_date", "desc")
            ->get();

        if ($usersUpcomingMedicalExams->isEmpty() && $usersOverdueMedicalExams->isEmpty()) {
            return;
        }

        foreach ($usersToNotify as $user) {
            $user->notify(new UpcomingAndOverdueMedicalExamsNotification($usersUpcomingMedicalExams, $usersOverdueMedicalExams));
        }
    }
}
