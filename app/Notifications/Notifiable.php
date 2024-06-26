<?php

declare(strict_types=1);

namespace Toby\Notifications;

interface Notifiable
{
    public function notify($instance);
}
