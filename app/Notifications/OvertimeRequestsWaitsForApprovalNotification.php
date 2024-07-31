<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Spatie\SlashCommand\AttachmentAction;
use Toby\Helpers\DateFormats;
use Toby\Slack\Elements\Attachment;
use Toby\Slack\Elements\SlackMessage;

class OvertimeRequestsWaitsForApprovalNotification extends OvertimeRequestNotification
{
    public function toSlack(): SlackMessage
    {
        $url = route("overtime.requests.show", ["overtimeRequest" => $this->overtimeRequest->id]);
        $seeDetails = __("See details");

        $actions = [
            AttachmentAction::create("overtime_technical_approval", __("Approve"), "button")
                ->setValue("overtime_technical_approval")
                ->setStyle("primary"),
            AttachmentAction::create("overtime_reject", __("Reject"), "button")
                ->setValue("overtime_reject")
                ->setStyle("danger"),
        ];

        $attachment = Attachment::create()
            ->setCallbackId("overtime:{$this->overtimeRequest->id}")
            ->setText(__("Available actions:"))
            ->setActions($actions);

        return (new SlackMessage())
            ->text("{$this->buildDescription()}\n <{$url}|{$seeDetails}>")
            ->withAttachment($attachment);
    }

    protected function buildDescription(): string
    {
        $title = $this->overtimeRequest->name;
        $requester = $this->overtimeRequest->user->profile->full_name;
        $from = $this->overtimeRequest->from;
        $to = $this->overtimeRequest->to;
        $hours = $this->overtimeRequest->hours;

        $date = "{$from->format(DateFormats::DATETIME_DISPLAY)} - {$to->format(DateFormats::DATETIME_DISPLAY)}";

        return __("The request :title is waiting for your technical approval.\nUser: :requester\nDate: :date (number of hours: :hours)", [
            "title" => $title,
            "requester" => $requester,
            "date" => $date,
            "hours" => $hours,
        ]);
    }
}
