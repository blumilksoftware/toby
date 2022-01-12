<?php

declare(strict_types=1);

return [
    "analyzers" => ["*"],
    "exclude_analyzers" => [],
    "ci_mode_exclude_analyzers" => [],
    "analyzer_paths" => [
        "Enlightn\\Enlightn\\Analyzers" => base_path("vendor/enlightn/enlightn/src/Analyzers"),
        "Enlightn\\EnlightnPro\\Analyzers" => base_path("vendor/enlightn/enlightnpro/src/Analyzers"),
    ],
    "base_path" => [
        app_path(),
        database_path("migrations"),
        database_path("seeders"),
    ],
    "skip_env_specific" => env("ENLIGHTN_SKIP_ENVIRONMENT_SPECIFIC", false),
    "guest_url" => null,
    "dont_report" => [],
    "ignore_errors" => [],
    "license_whitelist" => [
        "Apache-2.0",
        "Apache2",
        "BSD-2-Clause",
        "BSD-3-Clause",
        "LGPL-2.1-only",
        "LGPL-2.1",
        "LGPL-2.1-or-later",
        "LGPL-3.0",
        "LGPL-3.0-only",
        "LGPL-3.0-or-later",
        "MIT",
        "ISC",
        "CC0-1.0",
        "Unlicense",
        "WTFPL",
    ],
    "credentials" => [
        "username" => env("ENLIGHTN_USERNAME"),
        "api_token" => env("ENLIGHTN_API_TOKEN"),
    ],
    "github_repo" => env("ENLIGHTN_GITHUB_REPO"),
    "compact_lines" => true,
    "commercial_packages" => [
        "enlightn/enlightnpro",
    ],
    "allowed_permissions" => [
        base_path() => "775",
        app_path() => "775",
        resource_path() => "775",
        storage_path() => "775",
        public_path() => "775",
        config_path() => "775",
        database_path() => "775",
        base_path("routes") => "775",
        app()->bootstrapPath() => "775",
        app()->bootstrapPath("cache") => "775",
        app()->bootstrapPath("app.php") => "664",
        base_path("artisan") => "775",
        public_path("index.php") => "664",
        public_path("server.php") => "664",
    ],
    "writable_directories" => [
        storage_path(),
        app()->bootstrapPath("cache"),
    ],
];
