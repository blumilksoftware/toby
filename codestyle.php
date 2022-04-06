<?php

declare(strict_types=1);

use Blumilk\Codestyle\Config;
use Blumilk\Codestyle\Configuration\Defaults\LaravelPaths;

$paths = new LaravelPaths();
$config = new Config(
    paths: $paths->add("codestyle.php"),
);

return $config->config();
