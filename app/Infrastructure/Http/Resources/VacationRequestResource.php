<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Toby\Domain\States\VacationRequest\AcceptedByAdministrative;
use Toby\Domain\States\VacationRequest\AcceptedByTechnical;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\VacationTypeConfigRetriever;

class VacationRequestResource extends JsonResource
{
    public static $wrap = null;
    protected VacationTypeConfigRetriever $configRetriever;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->configRetriever = app(VacationTypeConfigRetriever::class);
    }

    public function toArray($request): array
    {
        $user = $request->user();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "user" => new SimpleUserResource($user),
            "type" => $this->type,
            "isVacation" => $this->configRetriever->isVacation($this->type),
            "state" => $this->state,
            "from" => $this->from->toDisplayString(),
            "to" => $this->to->toDisplayString(),
            "displayDate" => $this->getDate($this->from->toDisplayString(), $this->to->toDisplayString()),
            "comment" => $this->comment,
            "days" => VacationResource::collection($this->vacations),
            "can" => [
                "acceptAsTechnical" => $this->resource->state->canTransitionTo(AcceptedByTechnical::class)
                    && $user->can("acceptAsTechApprover", $this->resource),
                "acceptAsAdministrative" => $this->resource->state->canTransitionTo(AcceptedByAdministrative::class)
                    && $user->can("acceptAsAdminApprover", $this->resource),
                "reject" => $this->resource->state->canTransitionTo(Rejected::class)
                    && $user->can("reject", $this->resource),
                "cancel" => $this->resource->state->canTransitionTo(Cancelled::class)
                    && $user->can("cancel", $this->resource),
            ],
        ];
    }

    private function getDate(string $from, string $to): string
    {
        return ($from !== $to)
            ? "{$from} - {$to}"
            : $from;
    }
}
