<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;

class SaySomething extends SignatureHandler
{
    protected $signature = "toby powiedz {zdanie}";

    protected $description = "Powiedz zdanie";

    public function handle(Request $request): Response
    {
        $sentence = $this->getArgument("zdanie");

        return $this->respondToSlack($sentence)
            ->displayResponseToEveryoneOnChannel();
    }
}