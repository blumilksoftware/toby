<?php

declare(strict_types=1);

use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Support\Facades\Schedule;
use Toby\Console\Commands\Database\BackupPostgresDatabase;
use Toby\Console\Commands\SendDailySummaryToSlack;
use Toby\Console\Commands\SendNotificationAboutBenefitsReportCreation;
use Toby\Console\Commands\SendNotificationAboutUpcomingAndOverdueMedicalExams;
use Toby\Console\Commands\SendNotificationAboutUpcomingAndOverdueOhsTraining;
use Toby\Console\Commands\SendOvertimeRequestSummariesToApprovers;
use Toby\Console\Commands\SendVacationRequestSummariesToApprovers;
use Toby\Jobs\CheckYearPeriod;

Schedule::command(SendDailySummaryToSlack::class)
    ->when(config("services.slack.enabled"))
    ->weekdays()
    ->dailyAt("08:00")
    ->onOneServer();

Schedule::command(SendVacationRequestSummariesToApprovers::class)
    ->weekdays()
    ->dailyAt("08:30")
    ->onOneServer();

Schedule::command(SendOvertimeRequestSummariesToApprovers::class)
    ->weekdays()
    ->dailyAt("08:30")
    ->onOneServer();

Schedule::command(SendNotificationAboutUpcomingAndOverdueMedicalExams::class)
    ->monthlyOn(1, "08:00")
    ->onOneServer();

Schedule::command(SendNotificationAboutUpcomingAndOverdueOhsTraining::class)
    ->monthlyOn(1, "08:00")
    ->onOneServer();

Schedule::command(SendNotificationAboutBenefitsReportCreation::class)
    ->lastDayOfMonth("08:00")
    ->onOneServer();

Schedule::job(CheckYearPeriod::class)
    ->yearlyOn(1, 1, "01:00")
    ->onOneServer();

Schedule::command(PruneStaleTagsCommand::class)
    ->hourly()
    ->onOneServer();

$scheduledTask = Schedule::command(BackupPostgresDatabase::class)
    ->dailyAt(time: "05:00")
    ->withoutOverlapping()
    ->onOneServer()
    ->environments(["production"]);

$notifyOnFailure = config()->get("mail.database_backup.notify_on_failure");

if ($notifyOnFailure) {
    $emailAddress = config()->get("mail.database_backup.notification_email");

    $scheduledTask->emailOutputOnFailure($emailAddress);
}
