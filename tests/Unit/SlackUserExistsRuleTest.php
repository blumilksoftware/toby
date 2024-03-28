<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Toby\Models\User;
use Toby\Slack\Rules\SlackUserExistsRule;

class SlackUserExistsRuleTest extends TestCase
{
    use RefreshDatabase;

    protected SlackUserExistsRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = $this->app->make(SlackUserExistsRule::class);
    }

    public function testItPassesIfSlackUserExists(): void
    {
        $slackId = "U03AZBA3RKL";
        $user = User::factory()->hasProfile(["slack_id" => $slackId])->create();

        $fail = false;

        $this->rule->validate("user", $slackId, function () use (&$fail): void {
            $fail = true;
        });

        $this->assertFalse($fail, "User $user->name does not exist in toby");
    }

    public function testItFailsIfSlackUserDoesNotExist(): void
    {
        $user = User::factory()->hasProfile()->create();

        $invalidValues = [
            "U03AZBA3RKL",
            null,
        ];

        foreach ($invalidValues as $value) {
            $fail = false;
            $this->rule->validate("user", null, function () use (&$fail): void {
                $fail = true;
            });
            $this->assertTrue($fail, "User $user->name does not exist in toby");
        }
    }
}
