#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

php /application/artisan migrate --force && \
  php /application/artisan route:cache && \
  bash reload-config.sh
