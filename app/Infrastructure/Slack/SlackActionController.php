<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as IlluminateRequest;
use Spatie\SlashCommand\Controller as SlackController;
use Toby\Domain\Actions\VacationRequest\AcceptAsAdministrativeAction;
use Toby\Domain\Actions\VacationRequest\AcceptAsTechnicalAction;
use Toby\Domain\Actions\VacationRequest\RejectAction;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Slack\Traits\FindsUserBySlackId;

class SlackActionController extends SlackController
{
    use FindsUserBySlackId;
    use AuthorizesRequests;

    public function handleVacationRequestAction(IlluminateRequest $request, AcceptAsTechnicalAction $acceptAsTechnical, AcceptAsAdministrativeAction $acceptAsAdministrative, RejectAction $reject): JsonResponse
    {
        $this->verifyWithSigning($request);

        $payload = json_decode($request->payload, true);

        $userSlackId = $payload["user"]["id"];

        $user = $this->findUserBySlackId($userSlackId);

        $vacationRequestId = $payload["callback_id"];

        $action = $payload["actions"][0]["value"];

        $vacationRequest = VacationRequest::query()->findOrFail($vacationRequestId);

        switch ($action) {
            case "technical_approval":
                if ($user->cannot("acceptAsTechApprover", $vacationRequest)) {
                    return $this->prepareAuthorizationError();
                }

                if (!$vacationRequest->state->equals(WaitingForTechnical::class)) {
                    return $this->prepareActionError($vacationRequest);
                }
                $acceptAsTechnical->execute($vacationRequest, $user);
                $responseText = __("The request :title from user :requester has been approved by you as a technical approver.", [
                    "title" => $vacationRequest->name,
                    "requester" => $vacationRequest->user->profile->full_name,
                    "technical" => $user->profile->full_name,
                ]);

                break;
            case "administrative_approval":
                if ($user->cannot("acceptAsAdminApprover", $vacationRequest)) {
                    return $this->prepareAuthorizationError();
                }

                if (!$vacationRequest->state->equals(WaitingForAdministrative::class)) {
                    return $this->prepareActionError($vacationRequest);
                }
                $acceptAsAdministrative->execute($vacationRequest, $user);
                $responseText = __("The request :title from user :requester has been approved by you as an administrative approver.", [
                    "title" => $vacationRequest->name,
                    "requester" => $vacationRequest->user->profile->full_name,
                ]);

                break;
            case "reject":
                if ($user->cannot("reject", $vacationRequest)) {
                    return $this->prepareAuthorizationError();
                }

                if (!$vacationRequest->state->equals(WaitingForTechnical::class, WaitingForAdministrative::class)) {
                    return $this->prepareActionError($vacationRequest);
                }
                $reject->execute($vacationRequest, $user);
                $responseText = __("The request :title from user :requester has been rejected by you.", [
                    "title" => $vacationRequest->name,
                    "requester" => $vacationRequest->user->profile->full_name,
                ]);

                break;
            default:
                return $this->prepareUnrecognizedActionError();
        }

        return response()->json([
            "text" => $responseText,
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
}
