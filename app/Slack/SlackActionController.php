<?php

declare(strict_types=1);

namespace Toby\Slack;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as IlluminateRequest;
use Spatie\SlashCommand\Controller as SlackController;
use Toby\Actions\OvertimeRequest\AcceptAsTechnicalAction as OvertimeAcceptAsTechnicalAction;
use Toby\Actions\OvertimeRequest\RejectAction as OvertimeRejectAction;
use Toby\Actions\VacationRequest\AcceptAsAdministrativeAction;
use Toby\Actions\VacationRequest\AcceptAsTechnicalAction;
use Toby\Actions\VacationRequest\RejectAction;
use Toby\Helpers\DateFormats;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Slack\Traits\FindsUserBySlackId;
use Toby\States\OvertimeRequest\WaitingForTechnical as OvertimeWaitingForTechnical;
use Toby\States\VacationRequest\WaitingForAdministrative;
use Toby\States\VacationRequest\WaitingForTechnical;

class SlackActionController extends SlackController
{
    use FindsUserBySlackId;
    use AuthorizesRequests;

    public function handleAction(IlluminateRequest $request, AcceptAsTechnicalAction $acceptAsTechnical, AcceptAsAdministrativeAction $acceptAsAdministrative, RejectAction $reject, OvertimeAcceptAsTechnicalAction $overtimeAcceptAsTechnical, OvertimeRejectAction $overtimeRejectAction): JsonResponse
    {
        $this->verifyWithSigning($request);

        $payload = json_decode($request->payload, true);

        $userSlackId = $payload["user"]["id"];

        $user = $this->findUserBySlackId($userSlackId);

        [$type, $id] = explode(":", $payload["callback_id"]);

        $action = $payload["actions"][0]["value"];

        if ($type === "overtime") {
            $overtimeRequest = OvertimeRequest::query()->findOrFail($id);

            return match ($action) {
                "overtime_technical_approval" => $this->handleOvertimeTechnicalApproval($user, $overtimeRequest, $overtimeAcceptAsTechnical),
                "overtime_reject" => $this->handleOvertimeRejection($user, $overtimeRequest, $overtimeRejectAction),
                default => $this->prepareUnrecognizedActionError(),
            };
        }

        $vacationRequest = VacationRequest::query()->findOrFail($id);

        return match ($action) {
            "technical_approval" => $this->handleTechnicalApproval($user, $vacationRequest, $acceptAsTechnical),
            "administrative_approval" => $this->handleAdministrativeApproval($user, $vacationRequest, $acceptAsAdministrative),
            "reject" => $this->handleRejection($user, $vacationRequest, $reject),
            default => $this->prepareUnrecognizedActionError(),
        };
    }

    public function prepareOvertimeActionError(OvertimeRequest $overtimeRequest): JsonResponse
    {
        return response()->json([
            "text" => __("You cannot perform this action because the current status of the request :title by user :requester is :status.", [
                "title" => $overtimeRequest->name,
                "requester" => $overtimeRequest->user->profile->full_name,
                "status" => $overtimeRequest->state->label(),
            ]),
        ]);
    }

    protected function prepareAuthorizationError(): JsonResponse
    {
        return response()->json([
            "text" => __("You do not have permission to perform this action."),
        ]);
    }

    protected function prepareActionError(VacationRequest $vacationRequest): JsonResponse
    {
        return response()->json([
            "text" => __("You cannot perform this action because the current status of the request :title by user :requester is :status.", [
                "title" => $vacationRequest->name,
                "requester" => $vacationRequest->user->profile->full_name,
                "status" => $vacationRequest->state->label(),
            ]),
        ]);
    }

    protected function prepareUnrecognizedActionError(): JsonResponse
    {
        return response()->json([
            "text" => __("Unrecognized action."),
        ]);
    }

    protected function handleTechnicalApproval(User $user, VacationRequest $vacationRequest, AcceptAsTechnicalAction $acceptAsTechnical): JsonResponse
    {
        if ($user->cannot("acceptAsTechApprover", $vacationRequest)) {
            return $this->prepareAuthorizationError();
        }

        if (!$vacationRequest->state->equals(WaitingForTechnical::class)) {
            return $this->prepareActionError($vacationRequest);
        }

        $acceptAsTechnical->execute($vacationRequest, $user);

        $from = $vacationRequest->from;
        $to = $vacationRequest->to;

        $date = $from->equalTo($to)
            ? "{$from->toDisplayString()}"
            : "{$from->toDisplayString()} - {$to->toDisplayString()}";

        $days = $vacationRequest->vacations()->count();

        return response()->json([
            "text" => __("The request :title has been approved by you as a technical approver.\nUser: :requester\nDate: :date (number of days: :days)", [
                "title" => $vacationRequest->name,
                "requester" => $vacationRequest->user->profile->full_name,
                "date" => $date,
                "days" => $days,
            ]),
        ]);
    }

    protected function handleAdministrativeApproval(User $user, VacationRequest $vacationRequest, AcceptAsAdministrativeAction $acceptAsAdministrative): JsonResponse
    {
        if ($user->cannot("acceptAsAdminApprover", $vacationRequest)) {
            return $this->prepareAuthorizationError();
        }

        if (!$vacationRequest->state->equals(WaitingForAdministrative::class)) {
            return $this->prepareActionError($vacationRequest);
        }

        $acceptAsAdministrative->execute($vacationRequest, $user);

        $from = $vacationRequest->from;
        $to = $vacationRequest->to;

        $date = $from->equalTo($to)
            ? "{$from->toDisplayString()}"
            : "{$from->toDisplayString()} - {$to->toDisplayString()}";

        $days = $vacationRequest->vacations()->count();

        return response()->json([
            "text" => __("The request :title has been approved by you as an administrative approver.\nUser: :requester\nDate: :date (number of days: :days)", [
                "title" => $vacationRequest->name,
                "requester" => $vacationRequest->user->profile->full_name,
                "date" => $date,
                "days" => $days,
            ]),
        ]);
    }

    protected function handleRejection(User $user, VacationRequest $vacationRequest, RejectAction $reject): JsonResponse
    {
        if ($user->cannot("reject", $vacationRequest)) {
            return $this->prepareAuthorizationError();
        }

        if (!$vacationRequest->state->equals(WaitingForTechnical::class, WaitingForAdministrative::class)) {
            return $this->prepareActionError($vacationRequest);
        }

        $reject->execute($vacationRequest, $user);

        return response()->json([
            "text" => __("The request :title from user :requester has been rejected by you.", [
                "title" => $vacationRequest->name,
                "requester" => $vacationRequest->user->profile->full_name,
            ]),
        ]);
    }

    protected function handleOvertimeTechnicalApproval(User $user, OvertimeRequest $overtimeRequest, OvertimeAcceptAsTechnicalAction $acceptAsTechnical): JsonResponse
    {
        if ($user->cannot("acceptAsTechApprover", $overtimeRequest)) {
            return $this->prepareAuthorizationError();
        }

        if (!$overtimeRequest->state->equals(OvertimeWaitingForTechnical::class)) {
            return $this->prepareOvertimeActionError($overtimeRequest);
        }

        $acceptAsTechnical->execute($overtimeRequest, $user);

        $title = $overtimeRequest->name;
        $requester = $overtimeRequest->user->profile->full_name;
        $from = $overtimeRequest->from;
        $to = $overtimeRequest->to;
        $hours = $overtimeRequest->hours;

        $date = "{$from->format(DateFormats::DATETIME_DISPLAY)} - {$to->format(DateFormats::DATETIME_DISPLAY)}";

        return response()->json([
            "text" => __("The request :title has been approved by you as a technical approver.\nUser: :requester\nDate: :date (number of hours: :hours)", [
                "title" => $overtimeRequest->name,
                "requester" => $overtimeRequest->user->profile->full_name,
                "date" => $date,
                "hours" => $hours,
            ]),
        ]);
    }

    protected function handleOvertimeRejection(User $user, OvertimeRequest $overtimeRequest, OvertimeRejectAction $reject): JsonResponse
    {
        if ($user->cannot("reject", $overtimeRequest)) {
            return $this->prepareAuthorizationError();
        }

        if (!$overtimeRequest->state->equals(OvertimeWaitingForTechnical::class)) {
            return $this->prepareOvertimeActionError($overtimeRequest);
        }

        $reject->execute($overtimeRequest, $user);

        return response()->json([
            "text" => __("The request :title from user :requester has been rejected by you.", [
                "title" => $overtimeRequest->name,
                "requester" => $overtimeRequest->user->profile->full_name,
            ]),
        ]);
    }
}
