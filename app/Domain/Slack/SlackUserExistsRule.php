<?php

declare(strict_types=1);

namespace Toby\Domain\Slack;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use Toby\Eloquent\Models\Profile;

class SlackUserExistsRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $slackId = Str::between($value, "<@", "|");

        return Profile::query()->where("slack_id", $slackId)->exists();
    }

    public function message(): string
    {
        return "Użytkownik :input nie istnieje w tobym";
    }
}