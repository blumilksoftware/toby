<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Toby\Eloquent\Models\User;

class KeyHasBeenTakenNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $recipient,
        protected User $sender,
    ) {}

    public function via(): array
    {
        return ["slack"];
    }

    public function toSlack($notifiable): string
    {
        return __(":recipient takes key no :key from :sender", [
            "recipient" => $this->getName($this->recipient),
            "sender" => $this->getName($this->sender),
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
