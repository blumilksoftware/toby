<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console;

use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Toby\Infrastructure\Console\Commands\Database\BackupPostgresDatabase;
use Toby\Infrastructure\Console\Commands\SendDailySummaryToSlack;
use Toby\Infrastructure\Console\Commands\SendVacationRequestSummariesToApprovers;
use Toby\Infrastructure\Jobs\CheckYearPeriod;

class Kernel extends ConsoleKernel
{
    protected function commands(): void
    {
        $this->load(__DIR__ . "/Commands");
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SendDailySummaryToSlack::class)
            ->when(config("services.slack.enabled"))
            ->weekdays()
            ->dailyAt("08:00");

        $schedule->command(SendVacationRequestSummariesToApprovers::class)
            ->weekdays()
            ->dailyAt("08:30");

        $schedule->job(CheckYearPeriod::class)
            ->yearlyOn(1, 1, "01:00");

        $schedule->command(PruneStaleTagsCommand::class)->hourly();

        $this->scheduleDatabaseBackup($schedule);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function scheduleDatabaseBackup(Schedule $schedule): void
    {
        $scheduledTask = $schedule->command(BackupPostgresDatabase::class)
            ->dailyAt(time: "05:00")
            ->withoutOverlapping()
            ->onOneServer()
            ->environments(["production"]);

        $notifyOnFailure = config()->get("mail.database_backup.notify_on_failure");

        if ($notifyOnFailure) {
            $emailAddress = config()->get("mail.database_backup.notification_email");

            $scheduledTask->emailOutputOnFailure($emailAddress);
        }
    }
}
