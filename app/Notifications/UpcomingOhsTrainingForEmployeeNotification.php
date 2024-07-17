<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Toby\Models\User;
use Toby\Slack\Elements\SlackMessage;

class UpcomingOhsTrainingForEmployeeNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected User $user,
    ) {
        parent::__construct();
    }

    public function via(): array
    {
        return [Channels::SLACK];
    }

    public function toSlack(): SlackMessage
    {
        $lastOhsTrainingDate = $this->user->lastOhsTraining();

        return (new SlackMessage())
            ->text(__("The deadline for occupational ohs training for you is about to expire - :date (:difference days)", [
                "date" => $lastOhsTrainingDate->to->toDisplayString(),
                "difference" => (int)$lastOhsTrainingDate->to->diffInDays(Carbon::today(), true),
            ]));
    }
}
