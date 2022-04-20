<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
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
        return [
            "id" => $this->id,
            "name" => $this->name,
            "user" => new SimpleUserResource($this->user),
            "type" => $this->type,
            "isVacation" => $this->configRetriever->isVacation($this->type),
            "state" => $this->state,
            "from" => $this->from->toDisplayString(),
            "to" => $this->to->toDisplayString(),
            "comment" => $this->comment,
            "days" => VacationResource::collection($this->vacations),
        ];
    }
}
