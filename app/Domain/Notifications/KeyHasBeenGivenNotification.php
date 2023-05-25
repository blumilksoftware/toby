<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Illuminate\Bus\Queueable;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

class KeyHasBeenGivenNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected User $sender,
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
            ->text(__(":sender gives key no :key to :recipient", [
                "sender" => $this->getName($this->sender),
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
