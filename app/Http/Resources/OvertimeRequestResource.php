<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Toby\Helpers\DateFormats;
use Toby\States\OvertimeRequest\AcceptedByTechnical;
use Toby\States\OvertimeRequest\Cancelled;
use Toby\States\OvertimeRequest\Rejected;
use Toby\States\OvertimeRequest\Settled;

class OvertimeRequestResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $user = $request->user();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "user" => new SimpleUserResource($this->user),
            "state" => $this->state,
            "from" => $this->from->format(DateFormats::DATETIME),
            "to" => $this->to->format(DateFormats::DATETIME),
            "hours" => $this->hours,
            "settlementType" => $this->settlement_type,
            "settled" => $this->settled,
            "displayDate" => $this->getDate($this->from->toDisplayString(), $this->to->toDisplayString()),
            "comment" => $this->comment,
            "can" => [
                "acceptAsTechnical" => $this->resource->state->canTransitionTo(AcceptedByTechnical::class)
                    && $user->can("acceptAsTechApprover", $this->resource),
                "reject" => $this->resource->state->canTransitionTo(Rejected::class)
                    && $user->can("reject", $this->resource),
                "cancel" => $this->resource->state->canTransitionTo(Cancelled::class)
                    && $user->can("cancel", $this->resource),
                "settle" => $this->resource->state->canTransitionTo(Settled::class)
                    && $user->can("settle", $this->resource),
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
