<?php

declare(strict_types=1);

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    public static function prepare(): void
    {
        if (!static::runningInDocker()) {
            static::startChromeDriver();
        }
    }

    protected static function runningInDocker(): bool
    {
        return isset($_ENV["DUSK_IN_DOCKER"]) && $_ENV["DUSK_IN_DOCKER"] === "true";
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions())->addArguments(
            collect(
                [
                    "--window-size=1920,1080",
                ],
            )->unless(
                $this->hasHeadlessDisabled(),
                fn($items) => $items->merge(
                    [
                        "--disable-gpu",
                        "--headless",
                    ],
                ),
            )->all(),
        );

        return RemoteWebDriver::create(
            env("DUSK_DRIVER_URL") ?? "http://localhost:" . env("SELENIUM_PORT"),
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options,
            ),
        );
    }

    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER["DUSK_HEADLESS_DISABLED"]) ||
            isset($_ENV["DUSK_HEADLESS_DISABLED"]);
    }
}
