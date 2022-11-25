<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\VacationRequest;

class RemotesAttachment extends ListAttachment
{
    public function __construct(Collection $remoteDays)
    {
        parent::__construct();

        $this
            ->setTitle(__("Remote work :house_with_garden:"))
            ->setColor("#84cc16")
            ->setItems($remoteDays->map(fn(VacationRequest $vacation): string => $vacation->user->profile->full_name))
            ->setEmptyText(__("Everybody is in the office :boom:"));
    }
}
