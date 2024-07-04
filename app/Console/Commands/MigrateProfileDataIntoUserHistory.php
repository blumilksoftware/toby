<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;

class MigrateProfileDataIntoUserHistory extends Command
{
    protected $signature = "toby:move-user-data-to-history";
    protected $description = "Move profile data to user history";

    public function handle(): void
    {
        $users = User::query()
            ->with("profile")
            ->get();

        foreach ($users as $user) {
            $this->moveMedicalDataToHistory($user);
            $this->moveOhsDataToHistory($user);
        }
    }

    private function moveMedicalDataToHistory(User $user): void
    {
        if ($user->profile->last_medical_exam_date && $user->profile->next_medical_exam_date) {
            $user->histories()->create([
                "from" => $user->profile->last_medical_exam_date,
                "to" => $user->profile->next_medical_exam_date,
                "type" => UserHistoryType::MedicalExam,
            ]);
        }
    }

    private function moveOhsDataToHistory(User $user): void
    {
        if ($user->profile->last_ohs_training_date && $user->profile->next_ohs_training_date) {
            $user->histories()->create([
                "from" => $user->profile->last_ohs_training_date,
                "to" => $user->profile->next_ohs_training_date,
                "type" => UserHistoryType::OhsTraining,
            ]);
        }
    }
}
