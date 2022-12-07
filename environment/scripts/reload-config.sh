#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

php artisan config:clear && php artisan config:cache
