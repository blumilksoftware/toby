<?php

declare(strict_types=1);

namespace Toby\Slack\Elements;

use Illuminate\Support\Collection;

class AbsencesAttachment extends ListAttachment
{
    public function __construct(Collection $absences)
    {
        parent::__construct();

        $this
            ->setTitle(__("Absences :palm_tree:"))
            ->setColor("#eab308")
            ->setItems($absences->map(fn(array $vacation): string => $vacation["name"]))
            ->setEmptyText(__("Everybody works today :muscle:"));
    }
}
