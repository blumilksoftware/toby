<?php

declare(strict_types=1);

use Blumilk\Codestyle\Config;
use Blumilk\Codestyle\Configuration\Defaults\CommonRules;
use Blumilk\Codestyle\Configuration\Defaults\LaravelPaths;
use PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordFixer;

$paths = new LaravelPaths();
$rules = new CommonRules();

$config = new Config(
    paths: $paths->add("codestyle.php"),
    rules: $rules->filter(ClassKeywordFixer::class),
);

return $config->config();
