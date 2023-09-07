<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Spatie\SlashCommand\AttachmentAction;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Infrastructure\Slack\Elements\Attachment;
use Toby\Infrastructure\Slack\Elements\SlackMessage;

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

        if ($this->vacationRequest->state->equals(WaitingForTechnical::class)) {
            return __("The request :title from user :requester is waiting for your technical approval.", [
                "title" => $title,
                "requester" => $requester,
            ]);
        }

        return __("The request :title from user :requester is waiting for your administrative approval.", [
            "title" => $title,
            "requester" => $requester,
        ]);
    }
}
