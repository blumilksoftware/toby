<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Toby\Eloquent\Models\User;

class UpcomingAndOverdueMedicalExamsNotification extends QueuedNotification
{
    use Queueable;

    public function via(): array
    {
        return [Channels::MAIL];
    }

    public function toMail(Notifiable $notifiable): MailMessage
    {
        $url = route(
            "users.index",
        );

        return $this->buildMailMessage($notifiable, $url);
    }

    protected function buildMailMessage(Notifiable $notifiable, string $url): MailMessage
    {
        $user = $notifiable->profile->first_name;

        $usersUpcomingMedicalExams = User::query()
            ->whereRelation("profile", "next_medical_exam_date", ">", Carbon::now())
            ->whereRelation("profile", "next_medical_exam_date", "<=", Carbon::now()->addMonths(2))
            ->orderByProfileField("next_medical_exam_date", "desc")
            ->get();

        $usersOverdueMedicalExams = User::query()
            ->whereRelation("profile", "next_medical_exam_date", "<=", Carbon::now())
            ->orderByProfileField("next_medical_exam_date", "desc")
            ->get();

        $message = (new MailMessage())
            ->greeting(
                __("Hi :user!", [
                    "user" => $user,
                ]),
            )
            ->subject(__("Occupational medicine examinations for employees"));

        if ($usersUpcomingMedicalExams->isEmpty() && $usersOverdueMedicalExams->isEmpty())
        {
            $message->line(__("During the next two months, none of the employees have to go for medical examinations."));
        }

        if ($usersUpcomingMedicalExams->isNotEmpty())
        {
            $message->line(__("The deadline for occupational health examinations for some employees is about to expire."))
                ->line(__("Below is a list of employees with upcoming health examinations:"));

            foreach ($usersUpcomingMedicalExams as $user) {
                $message->line(__(":user - :date (in :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => $user->profile->next_medical_exam_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        if ($usersOverdueMedicalExams->isNotEmpty()) {
            $message->line(__("The deadline for medical examinations for some employees has passed."))
                ->line(__("Below is a list of employees with overdue examinations:"));

            foreach ($usersOverdueMedicalExams as $user) {
                $message->line(__(":user - :date (overdue :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => $user->profile->next_medical_exam_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        return $message
            ->action(__("See list of users"), $url);
    }
}
