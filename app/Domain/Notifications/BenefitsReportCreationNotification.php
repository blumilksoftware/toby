<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class BenefitsReportCreationNotification extends QueuedNotification
{
    use Queueable;

    public function via(): array
    {
        return [Channels::MAIL];
    }

    public function toMail(Notifiable $notifiable): MailMessage
    {
        $url = route(
            "assigned-benefits.index",
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
            ->subject(__("Reminder to create benefit report"))
            ->line(__("This message is a reminder to create a report for benefits for this month. If a report has already been created, please ignore this message."));

        return $message
            ->action(__("Benefits reports"), $url);
    }
}
