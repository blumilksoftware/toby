<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Carbon;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Actions\VacationRequest\CreateAction;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\Slack\SignatureHandler;
use Toby\Domain\Slack\Traits\FindsUserBySlackId;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class HomeOffice extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby zdalnie";
    protected $description = "Pracuj dzisiaj zdalnie";

    public function handle(Request $request): Response
    {
        $user = $this->findUserBySlackId($request->userId);

        $this->createRemoteday($user, Carbon::today());

        return $this->respondToSlack(":white_check_mark: Pracujesz dzisiaj zdalnie");
    }

    protected function createRemoteday(User $user, Carbon $date): void
    {
        $yearPeriod = YearPeriod::findByYear($date->year);

        app(CreateAction::class)->execute([
            "user_id" => $user->id,
            "type" => VacationType::HomeOffice,
            "from" => $date,
            "to" => $date,
            "year_period_id" => $yearPeriod->id,
            "flow_skipped" => false,
        ], $user);
    }
}