<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Toby\Eloquent\Models\User;

class KeyHasBeenGivenNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $sender,
        protected User $recipient,
    ) {}

    public function via(): array
    {
        return [Channels::SLACK];
    }

    public function toSlack(Notifiable $notifiable): string
    {
        return __(":sender gives key no :key to :recipient", [
            "sender" => $this->getName($this->sender),
            "recipient" => $this->getName($this->recipient),
            "key" => $notifiable->id,
        ]);
    }

    protected function getName(User $user): string
    {
        if ($user->profile->slack_id !== null) {
            return "<@{$user->profile->slack_id}>";
        }

        return $user->profile->full_name;
    }
}
