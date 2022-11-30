<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

class VacationRequestStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
        protected User $user,
    ) {}

    public function via(): array
    {
        return [Channels::MAIL, Channels::SLACK];
    }

    public function toSlack(): SlackMessage
    {
        $url = route("vacation.requests.show", ["vacationRequest" => $this->vacationRequest->id]);
        $seeDetails = __("See details");

        return (new SlackMessage())
            ->text("{$this->buildDescription()}\n <{$url}|{$seeDetails}>");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toMail(): MailMessage
    {
        $url = route(
            "vacation.requests.show",
            [
                "vacationRequest" => $this->vacationRequest,
            ],
        );

        return $this->buildMailMessage($url);
    }

    protected function buildMailMessage(string $url): MailMessage
    {
        $user = $this->user->profile->first_name;
        $type = $this->vacationRequest->type->label();
        $from = $this->vacationRequest->from;
        $to = $this->vacationRequest->to;
        $days = $this->vacationRequest->vacations()->count();

        $date = $from->equalTo($to)
            ? "{$from->toDisplayString()}"
            : "{$from->toDisplayString()} - {$to->toDisplayString()}";

        return (new MailMessage())
            ->greeting(__("Hi :user!", [
                "user" => $user,
            ]))
            ->subject($this->buildSubject())
            ->line($this->buildDescription())
            ->line(__("Request type: :type", [
                "type" => $type,
            ]))
            ->line(
                __("Date: :date (number of days: :days)", [
                    "date" => $date,
                    "days" => $days,
                ]),
            )
            ->action(__("Click here for details"), $url);
    }

    protected function buildSubject(): string
    {
        return __("Request :title has been :status", [
            "title" => $this->vacationRequest->name,
            "status" => $this->vacationRequest->state->label(),
        ]);
    }

    protected function buildDescription(): string
    {
        return __("The request :title from user :requester has been :status.", [
            "title" => $this->vacationRequest->name,
            "requester" => $this->vacationRequest->user->profile->full_name,
            "status" => $this->vacationRequest->state->label(),
        ]);
    }
}
