<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Infrastructure\Slack\Elements\SlackMessage;
use Toby\Infrastructure\Slack\Elements\VacationRequestsAttachment;

class VacationRequestsSummaryNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Carbon $day,
        protected Collection $vacationRequests,
    ) {}

    public function via(): array
    {
        return [Channels::MAIL, Channels::SLACK];
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage())
            ->text(__("Requests wait for your approval - status for day :date:", ["date" => $this->day->toDisplayString()]))
            ->withAttachment(new VacationRequestsAttachment($this->vacationRequests));
    }

    public function toMail(Notifiable $notifiable): MailMessage
    {
        $url = route(
            "vacation.requests.indexForApprovers",
            [
                "status" => "waiting_for_action",
            ],
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
            ->line  (__("Requests list waits for your approval - status for day :date:", ["date" => $this->day->toDisplayString()]))
            ->subject(__("Requests wait for your approval - status for day :date:", ["date" => $this->day->toDisplayString()]));

        foreach ($this->vacationRequests as $request) {
            $url = route("vacation.requests.show", ["vacationRequest" => $request->id]);

            $date = $request->from->equalTo($request->to)
                ? "{$request->from->toDisplayString()}"
                : "{$request->from->toDisplayString()} - {$request->to->toDisplayString()}";

            $message->line(
                __("[:request](:url) - :user (:date)", ["request" => $request->name, "url" => $url, "user" => $request->user->profile->full_name, "date" => $date]),
            );
        }

        return $message
            ->action(__("Go to requests"), $url);
    }
}
