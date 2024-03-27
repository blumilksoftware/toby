<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Spatie\SlashCommand\AttachmentAction;
use Toby\Slack\Elements\Attachment;
use Toby\Slack\Elements\SlackMessage;
use Toby\States\VacationRequest\WaitingForAdministrative;
use Toby\States\VacationRequest\WaitingForTechnical;

class VacationRequestWaitsForApprovalNotification extends VacationRequestNotification
{
    public function toSlack(): SlackMessage
    {
        $url = route("vacation.requests.show", ["vacationRequest" => $this->vacationRequest->id]);
        $seeDetails = __("See details");

        if ($this->vacationRequest->state->equals(WaitingForTechnical::class)) {
            $actions = [
                AttachmentAction::create("technical_approval", __("Approve"), "button")
                    ->setValue("technical_approval")
                    ->setStyle("primary"),
                AttachmentAction::create("reject", __("Reject"), "button")
                    ->setValue("reject")
                    ->setStyle("danger"),
            ];
        } elseif ($this->vacationRequest->state->equals(WaitingForAdministrative::class)) {
            $actions = [
                AttachmentAction::create("administrative_approval", __("Approve"), "button")
                    ->setValue("administrative_approval")
                    ->setStyle("primary"),
                AttachmentAction::create("reject", __("Reject"), "button")
                    ->setValue("reject")
                    ->setStyle("danger"),
            ];
        }

        $attachment = Attachment::create()
            ->setCallbackId((string)$this->vacationRequest->id)
            ->setText(__("Available actions:"))
            ->setActions($actions);

        return (new SlackMessage())
            ->text("{$this->buildDescription()}\n <{$url}|{$seeDetails}>")
            ->withAttachment($attachment);
    }

    protected function buildDescription(): string
    {
        $title = $this->vacationRequest->name;
        $requester = $this->vacationRequest->user->profile->full_name;
        $from = $this->vacationRequest->from;
        $to = $this->vacationRequest->to;

        $date = $from->equalTo($to)
            ? "{$from->toDisplayString()}"
            : "{$from->toDisplayString()} - {$to->toDisplayString()}";

        $days = $this->vacationRequest->vacations()->count();

        if ($this->vacationRequest->state->equals(WaitingForTechnical::class)) {
            return __("The request :title is waiting for your technical approval.\nUser: :requester\nDate: :date (number of days: :days)", [
                "title" => $title,
                "requester" => $requester,
                "date" => $date,
                "days" => $days,
            ]);
        }

        return __("The request :title is waiting for your administrative approval.\nUser: :requester\nDate: :date (number of days: :days)", [
            "title" => $title,
            "requester" => $requester,
            "date" => $date,
            "days" => $days,
        ]);
    }
}
