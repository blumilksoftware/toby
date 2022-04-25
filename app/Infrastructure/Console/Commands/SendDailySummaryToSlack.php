<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Spatie\SlashCommand\Attachment;
use Toby\Domain\DailySummaryRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;

class SendDailySummaryToSlack extends Command
{
    protected $signature = "toby:slack:daily-summary {--f|force}";
    protected $description = "Sent daily summary to slack";

    public function handle(DailySummaryRetriever $dailySummaryRetriever): void
    {
        $now = Carbon::today();

        if (!$this->option("force") && !$this->shouldHandle($now)) {
            return;
        }

        $absences = $dailySummaryRetriever->getAbsences($now)
            ->map(fn(Vacation $vacation) => $vacation->user->profile->full_name);

        $remoteDays = $dailySummaryRetriever->getRemoteDays($now)
            ->map(fn(Vacation $vacation) => $vacation->user->profile->full_name);

        $birthdays = $dailySummaryRetriever->getBirthdays($now)
            ->map(fn(User $user) => $user->profile->full_name);

        $absencesAttachment = Attachment::create()
            ->setTitle("Nieobecności :palm_tree:")
            ->setColor("#eab308")
            ->setText($absences->isNotEmpty() ? $absences->implode("\n") : "Wszyscy dzisiaj pracują :muscle:");

        $remoteAttachment = Attachment::create()
            ->setTitle("Praca zdalna :house_with_garden:")
            ->setColor("#d946ef")
            ->setText($remoteDays->isNotEmpty() ? $remoteDays->implode("\n") : "Wszyscy dzisiaj są w biurze :boom:");

        $birthdayAttachment = Attachment::create()
            ->setTitle("Urodziny :birthday:")
            ->setColor("#3C5F97")
            ->setText($birthdays->isNotEmpty() ? $birthdays->implode("\n") : "Dzisiaj nikt nie ma urodzin :cry:");

        $baseUrl = config("services.slack.url");
        $url = "{$baseUrl}/chat.postMessage";

        Http::withToken(config("services.slack.client_token"))
            ->post($url, [
                "channel" => config("services.slack.default_channel"),
                "text" => "Podsumowanie dla dnia {$now->toDisplayString()}",
                "attachments" => collect([$absencesAttachment, $remoteAttachment, $birthdayAttachment])->map(
                    fn(Attachment $attachment) => $attachment->toArray(),
                )->toArray(),
            ]);
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
}
