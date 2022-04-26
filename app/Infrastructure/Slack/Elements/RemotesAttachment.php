<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\Vacation;

class RemotesAttachment extends ListAttachment
{
    public function __construct(Collection $remoteDays)
    {
        parent::__construct();

        $this
            ->setTitle("Praca zdalna :house_with_garden:")
            ->setColor("#527aba")
            ->setItems($remoteDays->map(fn(Vacation $vacation): string => $vacation->user->profile->full_name))
            ->setEmptyText("Wszyscy dzisiaj są w biurze :boom:");
    }
}