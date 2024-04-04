<?php

declare(strict_types=1);

namespace Toby\Slack\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Toby\Models\Profile;

class SlackUserExistsRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $slackId = Str::between($value, "<@", "|");

        if (!Profile::query()->where("slack_id", $slackId)->exists()) {
            $fail(__("User :input does not exist in toby"));
        }
    }
}
