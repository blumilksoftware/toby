<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Enums\VacationType;
use Toby\Models\Holiday;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestsSummaryNotification;

class SendVacationRequestSummariesToApprovers extends Command
{
    protected $signature = "toby:send-vacation-request-reminders {--f|force}";
    protected $description = "Sends vacation request reminders to approvers if they didn't approve";

    public function handle(VacationTypeConfigRetriever $configRetriever): void
    {
        $now = Carbon::today();

        if (!$this->option("force") && !$this->shouldHandle($now)) {
            return;
        }

        $users = Permission::findByName("receiveVacationRequestsSummaryNotification")->users()->get();

        foreach ($users as $user) {
            $vacationRequests = VacationRequest::query()
                ->states(VacationRequestStatesRetriever::waitingForUserActionStates($user))
                ->orderByDesc("updated_at")
                ->get();

            if ($vacationRequests->isNotEmpty() && $this->worksToday($user, $now, $configRetriever)) {
                $user->notify(new VacationRequestsSummaryNotification(Carbon::today(), $vacationRequests));
            }
        }
    }

    protected function shouldHandle(CarbonInterface $day): bool
    {
        $holidays = Holiday::query()->whereDate("date", $day)->pluck("date");

        if ($day->isWeekend()) {
            return false;
        }

        if ($holidays->contains($day)) {
            return false;
        }

        return true;
    }

    protected function worksToday(User $user, Carbon $date, VacationTypeConfigRetriever $configRetriever): bool
    {
        $count = $user->vacationRequests()
            ->whereDate("from", "<=", $date)
            ->whereDate("to", ">=", $date)
            ->states(VacationRequestStatesRetriever::successStates())
            ->whereIn(
                "type",
                VacationType::all()->filter(fn(VacationType $type): bool => $configRetriever->isVacation($type)),
            )
            ->count();

        return $count === 0;
    }
}
