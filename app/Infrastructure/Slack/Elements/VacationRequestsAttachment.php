<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestsAttachment extends ListAttachment
{
    public function __construct(Collection $vacationRequests)
    {
        parent::__construct();

        $this
            ->setColor("#527aba")
            ->setItems($this->mapVacationRequests($vacationRequests));
    }

    protected function mapVacationRequests(Collection $vacationRequests): Collection
    {
        return $vacationRequests->map(function (VacationRequest $request): string {
            $url = route("vacation.requests.show", ["vacationRequest" => $request->id]);

            $date = $request->from->equalTo($request->to)
                ? "{$request->from->toDisplayString()}"
                : "{$request->from->toDisplayString()} - {$request->to->toDisplayString()}";

            return __("<:url|:request> - :user (:date)", [
                "url" => $url,
                "request" => $request->name,
                "user" => $request->user->profile->full_name,
                "date" => $date,
            ]);
        });
    }
}
