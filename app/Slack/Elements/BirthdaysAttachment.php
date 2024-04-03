<?php

declare(strict_types=1);

namespace Toby\Slack\Elements;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BirthdaysAttachment extends ListAttachment
{
    public function __construct(Collection $birthdays)
    {
        parent::__construct();

        $todayBirthdays = $birthdays->filter(
            fn(array $birthday): bool => Carbon::create($birthday["when"])->isToday(),
        );

        $nextUpcomingBirthday = $birthdays->first(
            fn(array $birthday): bool => Carbon::create($birthday["when"])->isAfter(Carbon::today()),
        );

        $this
            ->setTitle(__("Birthdays :birthday:"))
            ->setColor("#3c5f97")
            ->setItems($todayBirthdays->map(fn(array $birthday): string => $birthday["name"]));

        if ($todayBirthdays->isEmpty() && $nextUpcomingBirthday) {
            $this->addItem(__("Next birthdays: :relative", [
                "relative" => Carbon::create($nextUpcomingBirthday["when"])->diffForHumans(
                    Carbon::today(),
                    ["options" => CarbonInterface::ONE_DAY_WORDS, "syntax" => CarbonInterface::DIFF_RELATIVE_TO_NOW],
                ),
            ]));
        }
    }
}
