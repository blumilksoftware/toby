<?php

declare(strict_types=1);

namespace Toby\Architecture;

use Illuminate\Foundation\Exceptions\Handler;

class ExceptionHandler extends Handler
{
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];
}
