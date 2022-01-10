<?php

declare(strict_types=1);

namespace Toby\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];
}
