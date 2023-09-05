<?php

declare(strict_types=1);

use Toby\Infrastructure\Slack\Handlers\CatchAll;
use Toby\Infrastructure\Slack\Handlers\DailySummary;
use Toby\Infrastructure\Slack\Handlers\GiveKeysTo;
use Toby\Infrastructure\Slack\Handlers\Help;
use Toby\Infrastructure\Slack\Handlers\KeyList;
use Toby\Infrastructure\Slack\Handlers\LeaveKeysInOffice;
use Toby\Infrastructure\Slack\Handlers\RemoteWork;
use Toby\Infrastructure\Slack\Handlers\TakeKeysFrom;
use Toby\Infrastructure\Slack\Handlers\TakeKeysFromOffice;

return [
    "signing_secret" => env("SLACK_SIGNING_SECRET"),
    "handlers" => [
        TakeKeysFrom::class,
        GiveKeysTo::class,
        KeyList::class,
        RemoteWork::class,
        DailySummary::class,
        TakeKeysFromOffice::class,
        LeaveKeysInOffice::class,
        Help::class,
        CatchAll::class,
    ],
];
