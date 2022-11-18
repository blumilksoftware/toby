<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\VacationRequest;

class AbsencesAttachment extends ListAttachment
{
    public function __construct(Collection $absences)
    {
        parent::__construct();

        $this
            ->setTitle(__("Absences :palm_tree:"))
            ->setColor("#eab308")
            ->setItems($absences->map(fn(VacationRequest $vacation): string => $vacation->user->profile->full_name))
            ->setEmptyText(__("Everybody works today :muscle:"));
    }
}
