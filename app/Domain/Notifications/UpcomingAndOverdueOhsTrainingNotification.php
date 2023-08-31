<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Toby\Eloquent\Models\User;

class UpcomingAndOverdueOhsTrainingNotification extends QueuedNotification
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
        $now = Carbon::now();
        $user = $notifiable->profile->first_name;

        $usersForUpcomingOhsTraining = User::query()
            ->whereRelation("profile", "next_ohs_training_date", ">", $now)
            ->whereRelation("profile", "next_ohs_training_date", "<=", Carbon::now()->addMonths(2))
            ->orderByProfileField("next_ohs_training_date", "desc")
            ->get();

        $usersForOverdueOhsTraining = User::query()
            ->whereRelation("profile", "next_ohs_training_date", "<=", $now)
            ->orderByProfileField("next_ohs_training_date", "desc")
            ->get();

        $message = (new MailMessage())
            ->greeting(
                __("Hi :user!", [
                    "user" => $user,
                ]),
            )
            ->subject(__("Health and safety training for employees"));

        if ($usersForUpcomingOhsTraining->isEmpty() && $usersForOverdueOhsTraining->isEmpty())
        {
            $message->line(__("During the next two months, none of the employees have to go for OHS training."));
        }

        if ($usersForUpcomingOhsTraining->isNotEmpty())
        {
            $message->line(__("The deadline for OHS training for some employees is about to expire."))
                ->line(__("Below is a list of employees with upcoming OHS training:"));

            foreach ($usersForUpcomingOhsTraining as $user) {
                $message->line(__(":user - :date (in :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => $user->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        if ($usersForOverdueOhsTraining->isNotEmpty()) {
            $message->line(__("The deadline for OHS training for some employees has passed."))
                ->line(__("Below is a list of employees with overdue OHS training:"));

            foreach ($usersForOverdueOhsTraining as $user) {
                $message->line(__(":user - :date (overdue :difference days)", [
                    "user" => $user->profile->full_name,
                    "date" => $user->profile->next_ohs_training_date->toDisplayString(),
                    "difference" => $user->profile->next_ohs_training_date->diffInDays(Carbon::today()),
                ]));
            }
        }

        return $message
            ->action(__("See list of users"), $url);
    }
}
