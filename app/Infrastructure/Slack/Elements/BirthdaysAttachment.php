<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Eloquent\Models\User;

class BirthdaysAttachment extends ListAttachment
{
    public function __construct(Collection $birthdays)
    {
        parent::__construct();

        $todayBirthdays = $birthdays->filter(
            fn(User $user): bool => $user->upcomingBirthday()->isToday(),
        );

        $nextUpcomingBirthday = $birthdays->first(
            fn(User $user): bool => $user->upcomingBirthday()->isAfter(Carbon::today()),
        );

        $this
            ->setTitle(__("Birthdays :birthday:"))
            ->setColor("#3c5f97")
            ->setItems($todayBirthdays->map(fn(User $user): string => $user->profile->full_name));

        if ($todayBirthdays->isEmpty() && $nextUpcomingBirthday) {
            $this->addItem(__("Next birthdays: :relative", [
                "relative" => $nextUpcomingBirthday->upcomingBirthday()->diffForHumans(
                    Carbon::today(),
                    ["options" => CarbonInterface::ONE_DAY_WORDS, "syntax" => CarbonInterface::DIFF_RELATIVE_TO_NOW],
                ),
            ]));
        }
    }
}
