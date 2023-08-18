<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use InvalidArgumentException;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

abstract class VacationRequestNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
        protected User $user,
    ) {
        parent::__construct();
    }

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
            ->greeting(
                __("Hi :user!", [
                    "user" => $user,
                ])
            )
            ->subject($this->buildSubject())
            ->line($this->buildDescription())
            ->line(
                __("Request type: :type", [
                    "type" => $type,
                ])
            )
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
        return VacationRequestEmailTitle::get($this->vacationRequest->name);
    }

    abstract protected function buildDescription(): string;
}
