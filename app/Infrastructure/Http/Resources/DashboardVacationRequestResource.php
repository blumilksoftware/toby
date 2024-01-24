<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;

class DashboardVacationRequestResource extends JsonResource
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
            "user" => new SimpleUserResource($this->user),
            "type" => $this->type,
            "state" => $this->state,
            "from" => $this->from->toDisplayString(),
            "to" => $this->to->toDisplayString(),
            "displayDate" => $this->getDate($this->from->toDisplayString(), $this->to->toDisplayString()),
            "days" => VacationResource::collection($this->vacations),
            "pending" => $this->state->equals(...VacationRequestStatesRetriever::pendingStates()),
        ];
    }

    private function getDate(string $from, string $to): string
    {
        return ($from !== $to)
            ? "{$from} - {$to}"
            : $from;
    }
}
