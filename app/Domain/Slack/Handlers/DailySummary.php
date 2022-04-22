<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;

class DailySummary extends SignatureHandler
{
    protected $signature = "toby dzisiaj";

    protected $description = "Podsumowanie";

    public function handle(Request $request): Response
    {
        $configRetriever = app(VacationTypeConfigRetriever::class);

        $now = Carbon::today();

        /** @var Collection $absences */
        $absences = Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", $now)
            ->approved()
            ->whereTypes(VacationType::all()->filter(fn(VacationType $type) => $configRetriever->isVacation($type)))
            ->get()
            ->map(fn(Vacation $vacation) => $vacation->user->profile->full_name);

        /** @var Collection $remoteDays */
        $remoteDays = Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", $now)
            ->approved()
            ->whereTypes(VacationType::all()->filter(fn(VacationType $type) => !$configRetriever->isVacation($type)))
            ->get()
            ->map(fn(Vacation $vacation) => $vacation->user->profile->full_name);

        $birthdays = User::query()
            ->whereRelation("profile", "birthday", $now)
            ->get()
            ->map(fn(User $user) => $user->profile->full_name);

        $absencesAttachment = Attachment::create()
            ->setTitle("Nieobecności :palm_tree:")
            ->setColor('#eab308')
            ->setText($absences->isNotEmpty() ? $absences->implode("\n") : "Wszyscy dzisiaj pracują :muscle:");

        $remoteAttachment = Attachment::create()
            ->setTitle("Praca zdalna :house_with_garden:")
            ->setColor('#d946ef')
            ->setText($remoteDays->isNotEmpty() ? $remoteDays->implode("\n") : "Wszyscy dzisiaj są w biurze :boom:");

        $birthdayAttachment = Attachment::create()
            ->setTitle("Urodziny :birthday:")
            ->setColor('#3C5F97')
            ->setText($birthdays->isNotEmpty() ? $birthdays->implode("\n") : "Dzisiaj nikt nie ma urodzin :cry:");

        return $this->respondToSlack("Podsumowanie dla dnia {$now->toDisplayString()}")
            ->withAttachment($absencesAttachment)
            ->withAttachment($remoteAttachment)
            ->withAttachment($birthdayAttachment)
            ->displayResponseToEveryoneOnChannel();
    }
}