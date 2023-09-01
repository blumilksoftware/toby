<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\UpcomingAndOverdueMedicalExamsNotification;
use Toby\Eloquent\Models\User;

class SendNotificationAboutUpcomingAndOverdueMedicalExams extends Command
{
    protected $signature = "toby:send-notification-about-medical-exams";
    protected $description = "Send notifications about upcoming and overdue medical exams.";
    protected Collection $usersToNotify;
    protected Collection $usersUpcomingMedicalExams;
    protected Collection $usersOverdueMedicalExams;

    public function __construct()
    {
        parent::__construct();

        $this->usersToNotify = User::query()
            ->whereIn("role", [Role::AdministrativeApprover])
            ->get();

        $this->usersUpcomingMedicalExams = User::query()
            ->whereRelation("profile", "next_medical_exam_date", ">", Carbon::now())
            ->whereRelation("profile", "next_medical_exam_date", "<=", Carbon::now()->addMonths(2))
            ->orderByProfileField("next_medical_exam_date", "desc")
            ->get();

        $this->usersOverdueMedicalExams = User::query()
            ->whereRelation("profile", "next_medical_exam_date", "<=", Carbon::now())
            ->orderByProfileField("next_medical_exam_date", "desc")
            ->get();
    }

    public function handle(): void
    {
        if ($this->usersUpcomingMedicalExams->isEmpty() && $this->usersOverdueMedicalExams->isEmpty())
        {
            return;
        }

        foreach ($this->usersToNotify as $user)
        {
            $user->notify(new UpcomingAndOverdueMedicalExamsNotification(
                $this->usersUpcomingMedicalExams,
                $this->usersOverdueMedicalExams,
            ));
        }
    }
}
