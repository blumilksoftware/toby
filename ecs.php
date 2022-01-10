<?php

declare(strict_types=1);

use Blumilk\Codestyle\Config;
use Blumilk\Codestyle\Configuration\Defaults\LaravelPaths;

$paths = (new LaravelPaths())->add("public", "bootstrap/app.php", "ecs.php");
$config = new Config(
    paths: $paths,
);

return $config->config();
