<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

class VacationRequestEmailTitle
{
    public static function get(string $title): string
    {
        return __("Request :title in :application", [
            "title" => $title,
            "application" => config("app.name"),
        ]);
    }
}
