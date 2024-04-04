<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class UpcomingAndOverdueMedicalExamsNotification extends QueuedNotification
{
    use Queueable;

    protected Collection $usersUpcomingMedicalExams;
    protected Collection $usersOverdueMedicalExams;

    public function __construct(
        $usersUpcomingMedicalExams,
        $usersOverdueMedicalExams,
    ) {
        parent::__construct();

        $this->usersUpcomingMedicalExams = $usersUpcomingMedicalExams;
        $this->usersOverdueMedicalExams = $usersOverdueMedicalExams;
    }

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

        $message = (new MailMessage())
            ->greeting(
                __("Hi :user!", [
                    "user" => $user,
                ]),
            )
            ->subject(__("Occupational medicine examinations for employees"));

        if ($this->usersUpcomingMedicalExams->isEmpty() && $this->usersOverdueMedicalExams->isEmpty()) {
            $message->line(__("During the next two months, none of the employees have to go for medical examinations."));
        }

        if ($this->usersUpcomingMedicalExams->isNotEmpty()) {
            $message->line(__("The deadline for occupational health examinations for some employees is about to expire."))
                ->line(__("Below is a list of employees with upcoming health examinations:"));

            foreach ($this->usersUpcomingMedicalExams as $user) {
                $message->line(__(":user - :date (in :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => (int)$user->profile->next_medical_exam_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        if ($this->usersOverdueMedicalExams->isNotEmpty()) {
            $message->line(__("The deadline for medical examinations for some employees has passed."))
                ->line(__("Below is a list of employees with overdue examinations:"));

            foreach ($this->usersOverdueMedicalExams as $user) {
                $message->line(__(":user - :date (overdue :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_medical_exam_date->toDisplayString(),
                    "difference" => (int)$user->profile->next_medical_exam_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        return $message
            ->action(__("See list of users"), $url);
    }
}
