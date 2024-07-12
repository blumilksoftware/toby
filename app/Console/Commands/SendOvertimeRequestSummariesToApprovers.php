<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Models\Holiday;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Notifications\OvertimeRequestsSummaryNotification;

class SendOvertimeRequestSummariesToApprovers extends Command
{
    protected $signature = "toby:send-overtime-request-reminders {--f|force}";
    protected $description = "Sends overtime request reminders to approvers if they didn't approve";

    public function handle(VacationTypeConfigRetriever $configRetriever): void
    {
        $now = Carbon::today();

        if (!$this->option("force") && !$this->shouldHandle($now)) {
            return;
        }

        $users = Permission::findByName("receiveOvertimeRequestsSummaryNotification")->users()->get();

        foreach ($users as $user) {
            $overtimeRequests = OvertimeRequest::query()
                ->states(OvertimeRequestStatesRetriever::waitingForUserActionStates($user))
                ->orderByDesc("updated_at")
                ->get();

            if ($overtimeRequests->isNotEmpty() && $this->worksToday($user, $now)) {
                $user->notify(new OvertimeRequestsSummaryNotification(Carbon::today(), $overtimeRequests));
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

    protected function worksToday(User $user, Carbon $date): bool
    {
        $count = $user->overtimeRequests()
            ->whereDate("from", "<=", $date)
            ->whereDate("to", ">=", $date)
            ->states(OvertimeRequestStatesRetriever::successStates())
            ->count();

        return $count === 0;
    }
}
