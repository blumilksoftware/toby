<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Carbon;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\DailySummaryRetriever;
use Toby\Domain\Slack\SignatureHandler;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;

class DailySummary extends SignatureHandler
{
    protected $signature = "toby dzisiaj";
    protected $description = "Codzienne podsumowanie";

    public function handle(Request $request): Response
    {
        $dailySummaryRetriever = app()->make(DailySummaryRetriever::class);

        $now = Carbon::today();

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

        return $this->respondToSlack("Podsumowanie dla dnia {$now->toDisplayString()}")
            ->withAttachment($absencesAttachment)
            ->withAttachment($remoteAttachment)
            ->withAttachment($birthdayAttachment);
    }
}