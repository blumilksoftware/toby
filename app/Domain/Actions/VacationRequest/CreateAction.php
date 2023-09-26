<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Illuminate\Validation\ValidationException;
use Toby\Domain\Notifications\VacationRequestCreatedNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Domain\Validation\VacationRequestValidator;
use Toby\Domain\WorkDaysCalculator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class CreateAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationRequestValidator $vacationRequestValidator,
        protected VacationTypeConfigRetriever $configRetriever,
        protected WorkDaysCalculator $workDaysCalculator,
        protected WaitForTechApprovalAction $waitForTechApprovalAction,
        protected WaitForAdminApprovalAction $waitForAdminApprovalAction,
        protected ApproveAction $approveAction,
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $data, User $creator): VacationRequest
    {
        $vacationRequest = $this->createVacationRequest($data, $creator);
        $this->handleCreatedVacationRequest($vacationRequest);

        if ($this->configRetriever->isVacation($vacationRequest->type)) {
            $this->notify($vacationRequest);
        }

        return $vacationRequest;
    }

    /**
     * @throws ValidationException
     */
    protected function createVacationRequest(array $data, User $creator): VacationRequest
    {
        /** @var VacationRequest $vacationRequest */
        $vacationRequest = $creator->createdVacationRequests()->make($data);

        $this->vacationRequestValidator->validate($vacationRequest);

        $vacationRequest->save();

        $days = $this->workDaysCalculator->calculateDays(
            $vacationRequest->from,
            $vacationRequest->to,
            $vacationRequest->type,
        );

        foreach ($days as $day) {
            $vacationRequest->vacations()->create([
                "date" => $day,
                "user_id" => $vacationRequest->user->id,
                "year_period_id" => $vacationRequest->yearPeriod->id,
            ]);
        }

        $this->stateManager->markAsCreated($vacationRequest);

        return $vacationRequest;
    }

    protected function handleCreatedVacationRequest(VacationRequest $vacationRequest): void
    {
        if ($vacationRequest->hasFlowSkipped()) {
            $this->approveAction->execute($vacationRequest);

            return;
        }

        if ($this->configRetriever->needsTechnicalApproval($vacationRequest->type)) {
            $this->waitForTechApprovalAction->execute($vacationRequest);

            return;
        }

        if ($this->configRetriever->needsAdministrativeApproval($vacationRequest->type)) {
            $this->waitForAdminApprovalAction->execute($vacationRequest);

            return;
        }

        $this->approveAction->execute($vacationRequest);
    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $vacationRequest->user->notify(new VacationRequestCreatedNotification($vacationRequest));
    }
}
