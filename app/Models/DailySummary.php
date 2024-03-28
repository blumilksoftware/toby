<?php

declare(strict_types=1);

namespace Toby\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Slack\Elements\AbsencesAttachment;
use Toby\Slack\Elements\BirthdaysAttachment;
use Toby\Slack\Elements\RemotesAttachment;

/**
 * @property int $id
 * @property Carbon $day
 * @property Carbon $message_id
 * @property Carbon $channel_id
 * @property Collection $absences
 * @property Collection $remotes
 * @property Collection $birthdays
 */
class DailySummary extends Model
{
    protected $guarded = [];
    protected $casts = [
        "day" => "date",
        "absences" => "collection",
        "remotes" => "collection",
        "birthdays" => "collection",
    ];

    public function getAttachments(): Collection
    {
        return new Collection([
            new AbsencesAttachment($this->absences),
            new RemotesAttachment($this->remotes),
            new BirthdaysAttachment($this->birthdays),
        ]);
    }

    public function getTitle(): string
    {
        return __("Daily summary for day :day", ["day" => $this->day->toDisplayString()]);
    }
}
