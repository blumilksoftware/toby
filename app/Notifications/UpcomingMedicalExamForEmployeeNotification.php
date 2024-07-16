<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Toby\Models\User;
use Toby\Slack\Elements\SlackMessage;

class UpcomingMedicalExamForEmployeeNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected User $user,
    ) {
        parent::__construct();
    }

    public function via(): array
    {
        return [Channels::MAIL];
    }

    public function toSlack(): SlackMessage
    {
        $lastMedicalExamDate = $this->user->lastMedicalExam();

        return (new SlackMessage())
            ->text(__("The deadline for occupational health examinations for you is about to expire - :date (overdue :difference days)", [
                "date" => $lastMedicalExamDate->to->toDisplayString(),
                "difference" => (int)$lastMedicalExamDate->to->diffInDays(Carbon::today(), true),
            ]));
    }
}
