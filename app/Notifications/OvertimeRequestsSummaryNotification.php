<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Toby\Slack\Elements\OvertimeRequestsAttachment;
use Toby\Slack\Elements\SlackMessage;

class OvertimeRequestsSummaryNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected Carbon $day,
        protected Collection $overtimeRequests,
    ) {
        parent::__construct();
    }

    public function via(): array
    {
        return [Channels::SLACK];
    }

    public function toSlack(): SlackMessage
    {
        $this->loadRelations();

        return (new SlackMessage())
            ->text(__("Requests wait for your approval - status for day :date:", ["date" => $this->day->toDisplayString()]))
            ->withAttachment(new OvertimeRequestsAttachment($this->overtimeRequests));
    }

    protected function loadRelations(): void
    {
        $this->overtimeRequests->load(["user"]);
    }
}
