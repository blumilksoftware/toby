<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

class KeyHasBeenTakenFromTheOfficeNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $recipient,
    ) {}

    public function via(): array
    {
        return [Channels::SLACK];
    }

    public function toSlack(Notifiable $notifiable): SlackMessage
    {
        return (new SlackMessage())
            ->text(__(":recipient takes key no :key from the office", [
                "recipient" => $this->getName($this->recipient),
                "key" => $notifiable->id,
            ]));
    }

    protected function getName(User $user): string
    {
        if ($user->profile->slack_id !== null) {
            return "<@{$user->profile->slack_id}>";
        }

        return $user->profile->full_name;
    }
}
