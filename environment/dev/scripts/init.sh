#!/bin/bash
# This script have to be run from project root dir.

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

composer install

# check if APP_KEY is set, otherwise generate it
APP_KEY=$(grep APP_KEY .env | cut --delimiter '=' --fields 2-)

if [ -z "${APP_KEY}" ]; then
    echo "APP_KEY is not set. Creating:"
    php artisan key:generate
fi

php artisan migrate --seed
php artisan storage:link

echo "Running NPM stuff:"
npm install
