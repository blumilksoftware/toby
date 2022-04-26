<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\Vacation;

class AbsencesAttachment extends ListAttachment
{
    public function __construct(Collection $absences)
    {
        parent::__construct();

        $this
            ->setTitle("Nieobecności :palm_tree:")
            ->setColor("#eab308")
            ->setItems($absences->map(fn(Vacation $vacation): string => $vacation->user->profile->full_name))
            ->setEmptyText("Wszyscy dzisiaj pracują :muscle:");
    }
}