<?php

declare(strict_types=1);

namespace Toby\Domain\Slack;

use Illuminate\Support\Carbon;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Toby\Domain\Actions\VacationRequest\CreateAction;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class HomeOffice extends SignatureHandler
{
    protected $signature = "toby zdalnie {date=dzisiaj}";
    protected $description = "Praca zdalna {kiedy}";

    public function handle(Request $request): Response
    {
        $date = $this->getDateFromArgument($this->getArgument('date'));
        $user = $this->findUserBySlackId($request->userId);

        $yearPeriod = YearPeriod::findByYear($date->year);

        app(CreateAction::class)->execute([
            "user_id" => $user->id,
            "type" => VacationType::HomeOffice,
            "from" => $date,
            "to" => $date,
            "year_period_id" => $yearPeriod->id,
            "flow_skipped" => false,
        ], $user);

        return $this->respondToSlack("Praca zdalna dnia {$date->toDisplayString()} została utworzona pomyślnie");
    }

    protected function getDateFromArgument(string $argument): Carbon
    {
        return match ($argument) {
            "dzisiaj" => Carbon::today(),
            "jutro" => Carbon::tomorrow(),
            default => Carbon::create($argument),
        };
    }

    protected function findUserBySlackId(string $slackId): User
    {
        /** @var User $user */
        $user = User::query()
            ->whereRelation("profile", "slack_id", $slackId)
            ->first();

        return $user;
    }
}