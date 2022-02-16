<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
        protected User $user,
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
        $title = $this->vacationRequest->name;

        return (new MailMessage())
            ->greeting(__("Hi :user!", [
                "user" => $this->user->fullName,
            ]))
            ->subject(__("Vacation request :title", [
                "title" => $title,
            ]))
            ->line(__("Vacation request has been rejected.", [
            ]))
            ->action(__("Show vacation request"), $url);
    }
}
