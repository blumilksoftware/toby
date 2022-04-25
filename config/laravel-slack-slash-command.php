<?php

declare(strict_types=1);

use Toby\Domain\Slack\Handlers\CatchAll;
use Toby\Domain\Slack\Handlers\DailySummary;
use Toby\Domain\Slack\Handlers\GiveKeysTo;
use Toby\Domain\Slack\Handlers\Help;
use Toby\Domain\Slack\Handlers\HomeOffice;
use Toby\Domain\Slack\Handlers\KeyList;
use Toby\Domain\Slack\Handlers\TakeKeysFrom;

return [
    "signing_secret" => env("SLACK_SIGNING_SECRET"),
    "verify_with_signing" => true,
    "handlers" => [
        TakeKeysFrom::class,
        GiveKeysTo::class,
        KeyList::class,
        HomeOffice::class,
        DailySummary::class,
        Help::class,
        CatchAll::class,
    ],
];
