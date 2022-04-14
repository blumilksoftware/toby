<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
        protected User $user,
    ) {}

    public function via(): array
    {
        return ["mail"];
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
        $title = $this->vacationRequest->name;
        $type = $this->vacationRequest->type->label();
        $status = $this->vacationRequest->state->label();
        $from = $this->vacationRequest->from->toDisplayString();
        $to = $this->vacationRequest->to->toDisplayString();
        $days = $this->vacationRequest->vacations()->count();
        $requester = $this->vacationRequest->user->profile->full_name;

        return (new MailMessage())
            ->greeting(__("Hi :user!", [
                "user" => $user,
            ]))
            ->subject(__("Vacation request :title has been :status", [
                "title" => $title,
                "status" => $status,
            ]))
            ->line(__("The vacation request :title for user :requester has been :status.", [
                "title" => $title,
                "requester" => $requester,
                "status" => $status,
            ]))
            ->line(__("Vacation type: :type", [
                "type" => $type,
            ]))
            ->line(__("From :from to :to (number of days: :days)", [
                "from" => $from,
                "to" => $to,
                "days" => $days,
            ]))
            ->action(__("Click here for details"), $url);
    }
}
