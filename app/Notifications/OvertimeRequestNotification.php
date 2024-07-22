<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Slack\Elements\SlackMessage;

abstract class OvertimeRequestNotification extends QueuedNotification
{
    use Queueable;

    public function __construct(
        protected OvertimeRequest $overtimeRequest,
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
        $url = route("overtime.requests.show", ["overtimeRequest" => $this->overtimeRequest->id]);
        $seeDetails = __("See details");

        return (new SlackMessage())
            ->text("{$this->buildDescription()}\n <{$url}|{$seeDetails}>");
    }

    abstract protected function buildDescription(): string;
}
