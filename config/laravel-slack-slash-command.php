<?php

use Toby\Domain\Slack\GiveKeysTo;
use Toby\Domain\Slack\HomeOffice;
use Toby\Domain\Slack\KeyList;
use Toby\Domain\Slack\TakeKeysFrom;

return [
    'url' => 'api/slack',
    'signing_secret' => env('SLACK_SIGNING_SECRET'),
    'verify_with_signing' => true,
    'handlers' => [
        TakeKeysFrom::class,
        GiveKeysTo::class,
        KeyList::class,
        HomeOffice::class,
        Spatie\SlashCommand\Handlers\Help::class,
        Spatie\SlashCommand\Handlers\CatchAll::class,
    ],
];
