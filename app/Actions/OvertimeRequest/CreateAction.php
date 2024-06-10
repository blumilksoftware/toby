<?php

declare(strict_types=1);

namespace Toby\Actions\OvertimeRequest;

use Illuminate\Validation\ValidationException;
use Toby\Domain\OvertimeCalculator;
use Toby\Domain\OvertimeRequestStateManager;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Validation\OvertimeRequestValidator;

class CreateAction
{
    public function __construct(
        protected OvertimeRequestStateManager $stateManager,
        protected WaitForTechApprovalAction $waitForTechApprovalAction,
        protected OvertimeCalculator $overtimeCalculator,
        protected OvertimeRequestValidator $overtimeRequestValidator,
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $data, User $creator): OvertimeRequest
    {
        $overtimeRequest = $this->createVacationRequest($data, $creator);

        $this->handleCreatedOvertimeRequest($overtimeRequest);

        return $overtimeRequest;
    }

    /**
     * @throws ValidationException
     */
    protected function createVacationRequest(array $data, User $creator): OvertimeRequest
    {
        /** @var OvertimeRequest $overtimeRequest */
        $overtimeRequest = $creator->createdOvertimeRequests()->make($data);
        $overtimeRequest->hours = $this->overtimeCalculator->calculateHours($overtimeRequest->from, $overtimeRequest->to);

        $this->overtimeRequestValidator->validate($overtimeRequest);

        $overtimeRequest->save();

        $this->stateManager->markAsCreated($overtimeRequest);

        return $overtimeRequest;
    }

    protected function handleCreatedOvertimeRequest(OvertimeRequest $overtimeRequest): void
    {
        $this->waitForTechApprovalAction->execute($overtimeRequest);
    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $vacationRequest->user->notify(new VacationRequestCreatedNotification($vacationRequest));
    }
}
