<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Toby\Models\User;
use Toby\Slack\Elements\SlackMessage;

class KeyHasBeenTakenFromTheOfficeNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected User $recipient,
    ) {
        parent::__construct();
    }

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
