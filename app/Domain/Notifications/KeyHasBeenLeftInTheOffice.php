<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

class KeyHasBeenLeftInTheOffice extends Notification
{
    use Queueable;

    public function __construct(
        protected User $sender,
    ) {}

    public function via(): array
    {
        return [Channels::SLACK];
    }

    public function toSlack(Notifiable $notifiable): SlackMessage
    {
        return (new SlackMessage())
            ->text(__(":sender leaves key no :key in the office", [
                "sender" => $this->getName($this->sender),
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
