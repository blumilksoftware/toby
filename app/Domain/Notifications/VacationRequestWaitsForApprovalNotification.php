<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

class VacationRequestWaitsForApprovalNotification extends Notification
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
        $title = $this->vacationRequest->name;

        if ($this->vacationRequest->state->equals(WaitingForTechnical::class)) {
            return __("Request :title is waiting for your technical approval", [
                "title" => $title,
            ]);
        }

        return __("Request :title is waiting for your administrative approval", [
            "title" => $title,
        ]);
    }

    protected function buildDescription(): string
    {
        $title = $this->vacationRequest->name;
        $requester = $this->vacationRequest->user->profile->full_name;

        if ($this->vacationRequest->state->equals(WaitingForTechnical::class)) {
            return __("The request :title from user :requester is waiting for your technical approval.", [
                "title" => $title,
                "requester" => $requester,
            ]);
        }

        return __("The request :title from user :requester is waiting for your administrative approval.", [
            "title" => $title,
            "requester" => $requester,
        ]);
    }
}
