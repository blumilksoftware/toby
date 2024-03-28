<?php

declare(strict_types=1);

use Toby\Slack\Handlers\CatchAll;
use Toby\Slack\Handlers\DailySummary;
use Toby\Slack\Handlers\GiveKeysTo;
use Toby\Slack\Handlers\Help;
use Toby\Slack\Handlers\KeyList;
use Toby\Slack\Handlers\LeaveKeysInOffice;
use Toby\Slack\Handlers\RemoteWork;
use Toby\Slack\Handlers\TakeKeysFrom;
use Toby\Slack\Handlers\TakeKeysFromOffice;

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
