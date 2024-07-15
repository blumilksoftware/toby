<?php

declare(strict_types=1);

namespace Toby\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Helpers\DateFormats;
use Toby\Models\OvertimeRequest;

class OvertimeRequestsAttachment extends ListAttachment
{
    public function __construct(Collection $overtimeRequests)
    {
        parent::__construct();

        $this
            ->setColor("#527aba")
            ->setItems($this->mapOvertimeRequests($overtimeRequests));
    }

    protected function mapOvertimeRequests(Collection $overtimeRequests): Collection
    {
        return $overtimeRequests->map(function (OvertimeRequest $request): string {
            $url = route("overtime.requests.show", ["overtimeRequest" => $request->id]);

            $date = "{$request->from->format(DateFormats::DATETIME_DISPLAY)} - {$request->to->format(DateFormats::DATETIME_DISPLAY)}";

            return __("<:url|:request> - :user (:date)", [
                "url" => $url,
                "request" => $request->name,
                "user" => $request->user->profile->full_name,
                "date" => $date,
            ]);
        });
    }
}
