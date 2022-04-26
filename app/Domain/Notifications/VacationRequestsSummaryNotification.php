<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Eloquent\Models\User;

class VacationRequestsSummaryNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Carbon $day,
        protected Collection $vacationRequests,
    ) {
    }

    public function via(): array
    {
        return ["mail"];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route(
            "vacation.requests.indexForApprovers",
            [
                "status" => "waiting_for_action",
            ],
        );

        return $this->buildMailMessage($notifiable, $url);
    }

    protected function buildMailMessage(User $user, string $url): MailMessage
    {
        $user = $user->profile->first_name;

        $message = (new MailMessage())
            ->greeting(
                __("Hi :user!", [
                    "user" => $user,
                ])
            )
            ->line("Lista wniosków oczekujących na Twoją akcję - stan na dzień {$this->day->toDisplayString()}:")
            ->subject("Wnioski oczekujące na akcje - stan na dzień {$this->day->toDisplayString()}");

        foreach ($this->vacationRequests as $request) {
            $message->line(
                "Wniosek nr {$request->name} użytkownika {$request->user->profile->full_name} ({$request->from->toDisplayString()} - {$request->to->toDisplayString()})"
            );
        }

        return $message
            ->action("Przejdź do wniosków", $url);
    }
}
