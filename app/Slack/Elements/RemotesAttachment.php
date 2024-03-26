<?php

declare(strict_types=1);

namespace Toby\Slack\Elements;

use Illuminate\Support\Collection;

class RemotesAttachment extends ListAttachment
{
    public function __construct(Collection $remoteDays)
    {
        parent::__construct();

        $this
            ->setTitle(__("Remote work :house_with_garden:"))
            ->setColor("#84cc16")
            ->setItems($remoteDays->map(fn(array $vacation): string => $vacation["name"]))
            ->setEmptyText(__("Everybody is in the office :boom:"));
    }
}
