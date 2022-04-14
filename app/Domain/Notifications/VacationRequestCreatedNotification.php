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
        $user = $this->vacationRequest->user->profile->first_name;
        $type = $this->vacationRequest->type->label();
        $from = $this->vacationRequest->from->toDisplayString();
        $to = $this->vacationRequest->to->toDisplayString();
        $days = $this->vacationRequest->vacations()->count();

        return (new MailMessage())
            ->greeting(__("Hi :user!", [
                "user" => $user,
            ]))
            ->subject($this->buildSubject())
            ->line($this->buildDescription())
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

    protected function buildSubject(): string
    {
        $name = $this->vacationRequest->name;

        if ($this->vacationRequest->creator()->is($this->vacationRequest->user)) {
            return __("Vacation request :title has been created", [
                "title" => $name,
            ]);
        }

        return __("Vacation request :title has been created on your behalf", [
            "title" => $name,
        ]);
    }

    protected function buildDescription(): string
    {
        $name = $this->vacationRequest->name;
        $appName = config("app.name");

        if ($this->vacationRequest->creator()->is($this->vacationRequest->user)) {
            return __("The vacation request :title has been created correctly in the :appName.", [
                "title" => $name,
                "appName" => $appName,
            ]);
        }

        return __("The vacation request :title has been created correctly by user :creator on your behalf in the :appName.", [
            "title" => $this->vacationRequest->name,
            "appName" => $appName,
            "creator" => $this->vacationRequest->creator->profile->full_name,
        ]);
    }
}
