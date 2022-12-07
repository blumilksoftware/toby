#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

php /application/artisan config:clear && php /application/artisan config:cache
