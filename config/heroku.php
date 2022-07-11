<?php

declare(strict_types=1);

return [
    "release_version" => env("HEROKU_RELEASE_VERSION"),
    "slug_description" => env("HEROKU_SLUG_DESCRIPTION"),
    "release_created_at" => env("HEROKU_RELEASE_CREATED_AT"),
    "slug_commit" => env("HEROKU_SLUG_COMMIT"),
    "github_url" => env("GITHUB_REPO_URL"),
];
