<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
    ) {
    }

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
        $user = $this->vacationRequest->user->first_name;
        $title = $this->vacationRequest->name;
        $type = $this->vacationRequest->type->label();
        $from = $this->vacationRequest->from->toDisplayDate();
        $to = $this->vacationRequest->to->toDisplayDate();
        $days = $this->vacationRequest->vacations()->count();
        $appName = config("app.name");

        return (new MailMessage())
            ->greeting(__("Hi :user!", [
                "user" => $user,
            ]))
            ->subject(__("Vacation request :title has been created", [
                "title" => $title,
            ]))
            ->line(__("The vacation request :title has been created correctly in the :appName.", [
                "title" => $title,
                "appName" => $appName,
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
