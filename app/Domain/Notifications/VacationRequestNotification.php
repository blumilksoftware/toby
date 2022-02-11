<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestNotification extends Notification
{
    use Queueable;

    protected User $user;
    protected VacationRequest $vacationRequest;

    public function __construct(User $user, VacationRequest $vacationRequest)
    {
        $this->user = $user;
        $this->vacationRequest = $vacationRequest;
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
        $title = $this->vacationRequest->name;
        $state = $this->vacationRequest->state->label();

        $user = $this->user->getFullNameAttribute();

        return (new MailMessage())
            ->greeting(__("Hi :user!", [
                "user" => $user,
            ]))
            ->subject(__("Vacation request :title", [
                "title" => $title,
            ]))
            ->line(__("The vacation request :title has changed state to :state.", [
                "title" => $title,
                "state" => $state,
            ]))
            ->action(__("Show vacation request"), $url);
    }
}
