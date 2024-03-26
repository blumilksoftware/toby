<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler as HandlerContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Foundation\Application;
use Toby\Exceptions\ExceptionHandler;
use Toby\Console\Kernel as ConsoleKernel;
use Toby\Http\Kernel as HttpKernel;

$basePath = $_ENV["APP_BASE_PATH"] ?? dirname(__DIR__);
$application = new Application($basePath);

$application->singleton(HttpKernelContract::class, HttpKernel::class);
$application->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
$application->singleton(HandlerContract::class, ExceptionHandler::class);

return $application;
