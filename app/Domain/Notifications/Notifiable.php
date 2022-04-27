<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

interface Notifiable
{
    public function notify($instance);
}
