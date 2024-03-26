<?php

declare(strict_types=1);

namespace Toby\Slack\Handlers;

use Illuminate\Support\Carbon;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Actions\VacationRequest\CreateAction;
use Toby\Domain\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\YearPeriod;
use Toby\Slack\Traits\FindsUserBySlackId;

class RemoteWork extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby zdalnie";
    protected $description = "Work remotely today";

    public function handle(Request $request): Response
    {
        $user = $this->findUserBySlackId($request->userId);

        $this->createRemoteDay($user, Carbon::today());

        return $this->respondToSlack(__(":white_check_mark: You work remotely today"));
    }

    protected function createRemoteDay(User $user, Carbon $date): void
    {
        $yearPeriod = YearPeriod::findByYear($date->year);

        app(CreateAction::class)->execute([
            "user_id" => $user->id,
            "type" => VacationType::RemoteWork,
            "from" => $date,
            "to" => $date,
            "year_period_id" => $yearPeriod->id,
            "flow_skipped" => false,
        ], $user);
    }
}
