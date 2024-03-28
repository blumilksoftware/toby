<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class UpcomingAndOverdueOhsTrainingNotification extends QueuedNotification
{
    use Queueable;

    protected Collection $usersForUpcomingOhsTraining;
    protected Collection $usersForOverdueOhsTraining;

    public function __construct(
        $usersForUpcomingOhsTraining,
        $usersForOverdueOhsTraining,
    ) {
        parent::__construct();

        $this->usersForUpcomingOhsTraining = $usersForUpcomingOhsTraining;
        $this->usersForOverdueOhsTraining = $usersForOverdueOhsTraining;
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
            ->subject(__("Health and safety training for employees"));

        if ($this->usersForUpcomingOhsTraining->isEmpty() && $this->usersForOverdueOhsTraining->isEmpty()) {
            $message->line(__("During the next two months, none of the employees have to go for OHS training."));
        }

        if ($this->usersForUpcomingOhsTraining->isNotEmpty()) {
            $message->line(__("The deadline for OHS training for some employees is about to expire."))
                ->line(__("Below is a list of employees with upcoming OHS training:"));

            foreach ($this->usersForUpcomingOhsTraining as $user) {
                $message->line(__(":user - :date (in :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => (int)$user->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        if ($this->usersForOverdueOhsTraining->isNotEmpty()) {
            $message->line(__("The deadline for OHS training for some employees has passed."))
                ->line(__("Below is a list of employees with overdue OHS training:"));

            foreach ($this->usersForOverdueOhsTraining as $user) {
                $message->line(__(":user - :date (overdue :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => (int)$user->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        return $message
            ->action(__("See list of users"), $url);
    }
}
