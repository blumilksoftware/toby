#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

ARTISAN_PATH="/application/artisan"

php ${ARTISAN_PATH} migrate --force && \
php ${ARTISAN_PATH} route:cache && \
php ${ARTISAN_PATH} view:cache && \
php ${ARTISAN_PATH} event:cache && \
php ${ARTISAN_PATH} config:cache
